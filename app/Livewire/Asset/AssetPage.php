<?php

namespace App\Livewire\Asset;

use App\Enums\AuditEvent;
use App\Exports\AssetsExport;
use App\Models\Asset;
use App\Models\AssetDamage;
use App\Models\AssetRepair;
use App\Models\Category;
use App\Models\Location;
use App\Services\AuditService;
use App\Services\ImageService;
use App\Traits\HasAuthorization;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

#[Title('Assets')]
#[Layout('components.layouts.app')]
class AssetPage extends Component
{
    use WithFileUploads, WithPagination, HasAuthorization;
    public $search;
    public $perPage = 5;

    public $asset, $name, $asset_code;
    public $editIndex = '';
    public $showComponentModal = false;
    public $showLocationModal = false;

    public $showMoveForm = false;
    public $location_id, $moved_at;

    public $locations = [];
    public $components = [];

    // sort
    public $sortField = 'asset_code';
    public $sortDirection = 'desc';

    // filter
    public $filterCategory = '';
    public $filterCategoryDownload = '';
    public $filterLocation = '';
    public $filterLocationDownload = '';
    public $startDate = null;
    public $endDate = null;

    // QR Code
    public $showQrModal = false;
    public $assetQr;

    // Damage Modal
    public $showDamageModal = false;
    public $damageNotes = '';
    public $damageAssetId = null;
    public $damageImage = null;

    // export
    public $showModalPDF = false;
    public $showModalDetailAset = false;
    public $selectedAsset = null;
    protected $listeners = ['assetUpdated' => 'refreshAsset', 'closeDetailModal' => 'closeDetail'];

    // methods general
    public function sortBy($field)
    {
        $field = $mapping[$field] ?? $field;
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    public function resetFilters()
    {
        $this->filterCategory = '';
        $this->filterLocation = '';
    }
    private function resetPageIfEmpty()
    {
        if ($this->getQuery()->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
        }
    }
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    public function updatedFilterCategory()
    {
        $this->resetPage();
    }
    public function updatedFilterLocation()
    {
        $this->resetPage();
    }
    // methods asset
    public function create()
    {
        $this->requirePermission('tambah aset');
        return redirect()->route('asset.create');
    }

    public function edit($id)
    {
        $this->requirePermission('ubah aset');
        $asset = Asset::findOrFail($id);
        return redirect()->route('asset.edit', $asset);
    }

    public function cancelEdit()
    {
        $this->editIndex = '';
    }

    public function delete($id)
    {
        $this->requirePermission('hapus aset');
        $asset = Asset::findOrFail($id);
        $asset->delete();
        AuditService::log(
            AuditEvent::ASSET_DELETED,
            'asset',
            $asset,
            [
                'asset_code' => $asset->asset_code,
                'name' => $asset->name,
            ]
        );
        $this->resetPageIfEmpty();
        return redirect()->route('assets')->with('message', 'Asset deleted successfully.');
    }

    public function repairAsset($asset)
    {
        return redirect()->route('asset.repair.create', $asset);
    }

    public function showDetail($id)
    {
        $this->requirePermission('lihat detail aset');
        $this->showModalDetailAset = true;

        $this->selectedAsset = Asset::with(['components', 'locationHistories.location'])
            ->findOrFail($id);
    }

    public function closeDetail()
    {
        $this->showModalDetailAset = false;
        $this->selectedAsset = null;
    }

    // methods QR Code
    public function OpenModalQr($id)
    {
        $this->asset = Asset::findOrFail($id);
        $this->name = $this->asset->name;
        $this->asset_code = $this->asset->asset_code;
        $this->assetQr = base64_encode(QrCode::format('svg')
            ->size(200)
            ->generate($this->asset->asset_code));
        $this->showQrModal = true;
    }

    public function closeQrModal()
    {
        $this->reset(['showQrModal', 'asset', 'assetQr']);
    }

    // methods Damaged Asset
    public function openDamageModal($id)
    {
        $this->requirePermission('tandai aset rusak');
        $this->asset = Asset::findOrFail($id);
        $this->damageAssetId = $this->asset->id_asset;
        $this->damageNotes = '';
        $this->damageImage = null;
        $this->showDamageModal = true;
    }

    public function closeDamageModal()
    {
        $this->reset(['showDamageModal', 'damageNotes', 'damageAssetId', 'damageImage', 'asset']);
    }

    public function saveDamage(ImageService $imageService)
    {
        $this->validate([
            'damageNotes' => 'required|string',
            'damageImage' => 'nullable|image|max:2048', // Maksimal 2MB
        ]);
        $imagePath = null;
        if ($this->damageImage) {
            $imagePath = $imageService->uploadDamageAssetImage(
                file: $this->damageImage,
                damageAssetId: 'Damage_' . $this->damageAssetId . '_' . time()
            );
        }

        AssetDamage::create([
            'asset_id' => $this->damageAssetId,
            'damage_note' => $this->damageNotes,
            'image_path' => $imagePath,
            'reported_at' => now(),
        ]);

        Asset::where('id_asset', $this->damageAssetId)->update([
            'condition' => 'Rusak',
        ]);
        AuditService::log(
            AuditEvent::ASSET_DAMAGED,
            'asset',
            $this->asset,
            [
                'asset_code' => $this->asset->asset_code,
                'name' => $this->asset->name,
                'damage_note' => $this->damageNotes
            ]
        );
        $this->closeDamageModal();
    }

    public function resetImageDamage()
    {
        $this->damageImage = null;
    }

    public function assetStatistics()
    {
        return Asset::select('condition', DB::raw('COUNT(*) as total'))
            ->groupBy('condition')
            ->pluck('total', 'condition');
    }

    // methods export
    public function exportExcel()
    {
        return (new AssetsExport)->download('assets.xlsx');
    }

    public function openModalPDF()
    {
        $this->showModalPDF = true;
    }

    public function closeModalPDF()
    {
        $this->reset([
            'showModalPDF',
            'filterCategoryDownload',
            'filterLocationDownload',
            'startDate',
            'endDate'
        ]);
    }

    public function downloadPdf()
    {
        $assets = Asset::filter(
            $this->filterCategoryDownload,
            $this->filterLocationDownload,
            $this->startDate,
            $this->endDate
        )->get();


        $assets->transform(function ($asset) {

            $asset->name = mb_convert_encoding($asset->name, 'UTF-8');

            if ($asset->category) {
                $asset->category->name = mb_convert_encoding($asset->category->name, 'UTF-8');
            }

            if ($asset->latestLocation && $asset->latestLocation->location) {
                $asset->latestLocation->location->name =
                    mb_convert_encoding($asset->latestLocation->location->name, 'UTF-8', 'UTF-8');
            }

            return $asset;
        });

        $pdf = Pdf::loadView('exports.asset-export', [
            'assets' => $assets,
            'category' => $this->filterCategory,
            'location' => $this->filterLocation,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
        $this->reset([
            'filterCategoryDownload',
            'filterLocationDownload',
            'startDate',
            'endDate'
        ]);
        $filename = 'assets-' . Carbon::now()->format('d-m-Y') . '.pdf';
        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }

    private function getQuery()
    {
        return Asset::with(['category', 'components', 'latestLocation.location'])
            ->where('name', 'like', '%' . $this->search . '%')
            ->when($this->filterCategory, function ($query) {
                return $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterLocation, function ($query) {
                return $query->whereHas('latestLocation', function ($query) {
                    $query->where('location_id', $this->filterLocation);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        $assets = $this->getQuery();
        $categories = Category::select('id_category', 'name')->get();
        $filterLocations = Location::select('id_location', 'name')->get();
        $assetStats = $this->assetStatistics();
        return view('livewire.asset.asset-page', compact('assets', 'categories', 'filterLocations', 'assetStats'));
    }
}
