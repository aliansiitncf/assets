<?php

namespace App\Livewire\Asset;

use App\Enums\AuditEvent;
use App\Exports\AssetRepairExport;
use App\Models\Asset;
use App\Models\AssetRepair as AssetRepairModel;
use App\Models\Category;
use App\Models\Location;
use App\Services\AuditService;
use App\Traits\HasAuthorization;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Asset Repair')]
#[Layout('components.layouts.app')]
class PageAssetRepair extends Component
{
    use HasAuthorization, WithPagination;

    public $search = '';

    public $perPage = 10;

    public $showModalPDF = false;

    public $selectedAssetId = null;

    public $assetRepairsForSelected = [];

    // Props untuk filter tanggal
    public $startDate;

    public $endDate;

    // Props untuk filter kategori
    public $categoryFilter = '';

    public $categories = [];

    // props detail modal repair
    public $isDetailOpen = false;

    public $selectedRepair = null;

    // props filter location
    public $locationFilter = '';

    public $locations = [];

    public function mount()
    {
        $this->requirePermission('lihat aset perbaikan');
        $this->locations = Location::all();
        $this->categories = Category::orderBy('name')->get();
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
                'completed_at' => $assetRepair->completed_at->format('d M Y H:i'),
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
            'endDate',
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
        $pdf = Pdf::loadView('exports.asset-repair-export', [
            'assetRepairs' => $assetRepairs,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
        $this->reset([
            'startDate',
            'endDate',
        ]);
        $filename = 'asset-repair-' . Carbon::now()->format('d-m-Y') . '.pdf';

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }

    public function exportRepairExcel($assetId)
    {
        $asset = Asset::findOrFail($assetId);
        $filename = 'repair-' . $asset->asset_code . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new AssetRepairExport($assetId),
            $filename
        );
    }

    public function render()
    {
        $assets = Asset::query()
            ->whereHas('repairs')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('asset_code', 'like', "%{$this->search}%");
                });
            })
            ->when($this->locationFilter, function ($query) {
                $query->whereHas('latestLocation.location', function ($q) {
                    $q->where('id_location', $this->locationFilter);
                });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('id_category', $this->categoryFilter);
            })
            ->when($this->startDate, function ($query) {
                $query->whereHas('repairs', fn($q) => $q->whereDate('started_at', '>=', $this->startDate));
            })
            ->when($this->endDate, function ($query) {
                $query->whereHas('repairs', fn($q) => $q->whereDate('started_at', '<=', $this->endDate));
            })
            ->withCount('repairs')
            ->with(['latestLocation.location', 'category'])
            ->paginate($this->perPage);

        return view('livewire.asset.page-asset-repair', compact('assets'));
    }

    public function toggleAsset($assetId)
    {
        if ($this->selectedAssetId === $assetId) {
            $this->selectedAssetId = null;
            $this->assetRepairsForSelected = [];

            return;
        }

        $this->selectedAssetId = $assetId;
        $this->assetRepairsForSelected = AssetRepairModel::where('asset_id', $assetId)
            ->when($this->startDate, fn($q) => $q->whereDate('started_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('started_at', '<=', $this->endDate))
            ->latest('started_at')
            ->get();
    }

    public function showDetail($id)
    {
        $this->selectedRepair = AssetRepairModel::with(['asset.latestLocation.location', 'components'])
            ->findOrFail($id);
        $this->selectedRepair->components->each(function ($component) {
            $component->pivot->load('vendor', 'technician');
        });
        $this->isDetailOpen = true;
    }

    public function closeDetailModal()
    {
        $this->isDetailOpen = false;
        $this->selectedRepair = null;
    }

    // filter

    public function updatedSearch()
    {
        $this->selectedAssetId = null;
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->selectedAssetId = null;
        $this->resetPage();
        $this->dispatch('charts-updated');
    }

    public function updatedEndDate()
    {
        $this->selectedAssetId = null;
        $this->resetPage();
        $this->dispatch('charts-updated');
    }

    public function updatedLocationFilter()
    {
        $this->selectedAssetId = null;
        $this->resetPage();
        $this->dispatch('charts-updated');
    }

    public function updatedCategoryFilter()
    {
        $this->selectedAssetId = null;
        $this->resetPage();
        $this->dispatch('charts-updated');
    }

    public function resetFilter()
    {
        $this->reset(['startDate', 'endDate', 'categoryFilter', 'search', 'locationFilter', 'perPage']);
        $this->resetPage();
        $this->dispatch('charts-updated');
    }

    // Grafik
    public function getPoinChartDataProperty()
    {
        $query = AssetRepairModel::select('asset_id', DB::raw('SUM(poin) as total_poin'))
            ->groupBy('asset_id')
            ->with('asset:id_asset,asset_code,name')
            ->when($this->locationFilter, fn($q) => $q->whereHas('asset.latestLocation.location', function ($q) {
                $q->where('id_location', $this->locationFilter);
            }))
            ->when($this->categoryFilter, fn($q) => $q->whereHas('asset.category', function ($q) {
                $q->where('id_category', $this->categoryFilter);
            }))
            ->when($this->startDate, fn($q) => $q->whereDate('started_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('started_at', '<=', $this->endDate));

        $data = $query->orderByDesc('total_poin')->limit(10)->get();

        return [
            'labels' => $data->map(fn($item) => $item->asset?->name . ' (' . $item->asset?->asset_code . ')'),
            'values' => $data->pluck('total_poin'),
        ];
    }

    public function getBiayaChartDataProperty()
    {
        $query = AssetRepairModel::with(['asset:id_asset,asset_code,name', 'components'])
            ->when($this->locationFilter, fn($q) => $q->whereHas('asset.latestLocation.location', function ($q) {
                $q->where('id_location', $this->locationFilter);
            }))
            ->when($this->categoryFilter, fn($q) => $q->whereHas('asset.category', function ($q) {
                $q->where('id_category', $this->categoryFilter);
            }))
            ->when($this->startDate, fn($q) => $q->whereDate('started_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('started_at', '<=', $this->endDate));

        $data = $query->get()
            ->groupBy('asset_id')
            ->map(function ($repairs, $assetId) {
                $asset = $repairs->first()->asset;
                $total = $repairs->flatMap->components
                    ->sum(fn($c) => $c->pivot->qty * $c->pivot->price);

                return [
                    'label' => optional($asset)->name . ' (' . optional($asset)->asset_code . ')',
                    'total' => $total,
                ];
            })
            ->sortByDesc('total')
            ->take(10)
            ->values();

        return [
            'labels' => $data->pluck('label'),
            'values' => $data->pluck('total'),
        ];
    }
}
