<?php

namespace App\Livewire\Asset;

use App\Enums\AuditEvent;
use App\Models\Asset;
use App\Models\AssetRepair as AssetRepairModel;
use App\Services\AuditService;
use App\Traits\HasAuthorization;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Asset Repair')]
#[Layout('components.layouts.app')]
class PageAssetRepair extends Component
{
    use HasAuthorization, WithPagination;
    public $search = '';
    public $perPage = 10;
    public $showModalPDF = false;

    // Props untuk filter tanggal
    public $startDate;
    public $endDate;

    // Props untuk filter kategori
    public $categoryFilter = '';

    // props detail modal repair
    public $isDetailOpen = false;
    public $selectedRepair = null;

    public function mount()
    {
        $this->requirePermission('lihat aset perbaikan');
    }

    public function completeRepair($repairId)
    {
        $assetRepair = AssetRepairModel::find($repairId);
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
            ]
        );
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
        $assetRepairs = AssetRepairModel::filter(
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
        $assetRepairs = AssetRepairModel::query()
            ->with(['asset.latestLocation.location'])
            ->when($this->search, function ($query) {
                $query->whereHas('asset', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('asset_code', 'like', "%{$this->search}%");
                });
            })
            ->when($this->startDate, fn($query) => $query->whereDate('started_at', '>=', $this->startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('started_at', '<=', $this->endDate))
            ->latest('started_at')
            ->paginate($this->perPage);


        return view('livewire.asset.page-asset-repair', compact('assetRepairs'));
    }

    public function showDetail($id)
    {
        $this->selectedRepair = AssetRepairModel::with(['asset.latestLocation.location', 'components'])
            ->findOrFail($id);
        $this->isDetailOpen = true;
    }

    public function closeDetailModal()
    {
        $this->isDetailOpen = false;
        $this->selectedRepair = null;
    }

    //filter
    public function updatedStartDate()
    {
        $this->resetPage();
        $this->dispatch('charts-updated');
    }

    public function updatedEndDate()
    {
        $this->resetPage();
        $this->dispatch('charts-updated');
    }

    public function resetFilter()
    {
        $this->reset(['startDate', 'endDate', 'categoryFilter', 'search']);
        $this->resetPage();
        $this->dispatch('charts-updated');
    }


    // Grafik
    public function getPoinChartDataProperty()
    {
        $query = AssetRepairModel::select('asset_id', DB::raw('SUM(poin) as total_poin'))
            ->groupBy('asset_id')
            ->with('asset:id_asset,asset_code')
            ->when($this->startDate, fn($q) => $q->whereDate('started_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('started_at', '<=', $this->endDate));

        $data = $query->orderByDesc('total_poin')->limit(10)->get();

        return [
            'labels' => $data->pluck('asset.asset_code'),
            'values' => $data->pluck('total_poin'),
        ];
    }

    public function getBiayaChartDataProperty()
    {
        $query = AssetRepairModel::with(['asset:id_asset,asset_code', 'components'])
            ->when($this->startDate, fn($q) => $q->whereDate('started_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('started_at', '<=', $this->endDate));

        $data = $query->get()
            ->groupBy('asset_id')
            ->map(function ($repairs, $assetId) {
                $total = $repairs->flatMap->components
                    ->sum(fn($c) => $c->pivot->qty * $c->pivot->price);

                return [
                    'asset_code' => optional($repairs->first()->asset)->asset_code,
                    'total' => $total,
                ];
            })
            ->sortByDesc('total')
            ->take(10)
            ->values();

        return [
            'labels' => $data->pluck('asset_code'),
            'values' => $data->pluck('total'),
        ];
    }
}
