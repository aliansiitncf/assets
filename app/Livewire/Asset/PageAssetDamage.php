<?php

namespace App\Livewire\Asset;

use App\Models\AssetDamage;
use App\Traits\HasAuthorization;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Asset Damaged')]
#[Layout('components.layouts.app')]
class PageAssetDamage extends Component
{
    use HasAuthorization;
    public $search;
    public $perPage = 10;
    public $showModalPDF = false;
    public $startDate = null;
    public $endDate = null;
    public function mount()
    {
        $this->requirePermission('lihat aset rusak');
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
        $assetDamages = AssetDamage::filter(
            $this->startDate,
            $this->endDate
        )->get();

        // ✅ GENERATE PDF
        $pdf = Pdf::loadView('exports.asset-damaged-export', [
            'assetDamages' => $assetDamages,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
        $this->reset([
            'startDate',
            'endDate'
        ]);
        $filename = 'assets-damage-' . Carbon::now()->format('d-m-Y') . '.pdf';
        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }
    public function render()
    {
        $assetDamages = AssetDamage::with('asset')
            ->when($this->search, function ($query) {
                $query->whereHas('asset', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('asset_code', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('reported_at', 'desc')
            ->paginate($this->perPage);
        return view('livewire.asset.page-asset-damage', [
            'assetDamages' => $assetDamages,
        ]);
    }
}
