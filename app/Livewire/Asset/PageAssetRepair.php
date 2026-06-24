<?php

namespace App\Livewire\Asset;

use App\Enums\AuditEvent;
use App\Models\Asset;
use App\Models\AssetRepair;
use App\Services\AuditService;
use App\Traits\HasAuthorization;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Asset Repair')]
#[Layout('components.layouts.app')]
class PageAssetRepair extends Component
{
    use HasAuthorization;
    public $search = '';
    public $perPage = 10;
    public $showModalPDF = false;
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->requirePermission('lihat aset perbaikan');
    }

    public function completeRepair($repairId)
    {
        $assetRepair = AssetRepair::find($repairId);
        if ($assetRepair && $assetRepair->status === 'In Progress') {
            $assetRepair->status = 'Completed';
            $assetRepair->completed_at = now();
            $assetRepair->save();
            session()->flash('success', 'Asset repair marked as completed.');
        } else {
            session()->flash('error', 'Asset repair not found or already completed.');
        }
        Asset::where('id_asset', $assetRepair->asset_id)
            ->update(['condition' => 'Baik']);
        AuditService::log(
            AuditEvent::ASSET_REPAIR_COMPLETED,
            'asset',
            $assetRepair->asset,
            [
                'asset_code' => $assetRepair->asset->asset_code,
                'name' => $assetRepair->asset->name,
                'completed_at' => $assetRepair->completed_at->format('d M Y H:i')
            ]);
    }
    public function openModalPDF()
    {
        $this->showModalPDF = true;
    }
    public function closeModalPDF()
    {
        $this->reset([
            'showModalPDF',
            'startDate',
            'endDate'
        ]);
    }
    public function downloadPdf()
    {
        // ✅ FILTER (pakai when di model)
        $assetRepairs = AssetRepair::filter(
            $this->startDate,
            $this->endDate
        )->get();

        // ✅ GENERATE PDF
        $pdf = Pdf::loadView('exports.asset-Repair-export', [
            'assetRepairs' => $assetRepairs,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
        $this->reset([
            'startDate',
            'endDate'
        ]);
        $filename = 'asset-repair-' . Carbon::now()->format('d-m-Y') . '.pdf';
        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }
    public function render()
    {
        $assetRepairs = AssetRepair::with('asset')
            ->when($this->search, function ($query) {
                $query->whereHas('asset', function ($q) {
                    $q->where('asset_code', 'like', '%' . $this->search . '%')
                        ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate($this->perPage);
        return view('livewire.asset.page-asset-repair', [
            'assetRepairs' => $assetRepairs
        ]);
    }
}
