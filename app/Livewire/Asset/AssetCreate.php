<?php

namespace App\Livewire\Asset;

use App\Enums\AuditEvent;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Component as ComponentModel;
use App\Models\Detail;
use App\Models\Location;
use App\Services\AuditService;
use App\Services\ImageService;
use Intervention\Image\Image;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app'), Title('Create Asset')]
class AssetCreate extends Component
{
    use WithFileUploads;
    // Assets
    public $asset_code, $categories, $purchase_date, $name, $image, $category_id;

    // Components
    public $name_component, $components = [];

    // Asset Location Histories
    public $locations, $location_id, $details, $moved_at;

    public $search = '';
    public $results;

    // Detail tambahan (tab Detail): array of ['name' => '', 'value' => '']
    public array $detailItems = [];

    // status buka/tutup dropdown, di-entangle dengan Alpine di blade
    public bool $isOpen = false;

    // jumlah maksimal opsi yang ditampilkan sekali render
    protected int $componentLimit = 10;

    public function mount()
    {
        $this->categories = Category::select('id_category', 'name')->get();
        $this->locations = Location::select('id_location', 'name')->get();
        $this->generateAssetCodeAndPurchaseDate();

        $this->results = collect();
        $this->detailItems = [];
    }

    /**
     * Dipanggil saat input diklik (belum tentu ada ketikan).
     * Menampilkan daftar default (belum difilter) begitu dropdown dibuka.
     */
    public function openDropdown()
    {
        $this->isOpen = true;
        $this->loadResults($this->search);
    }

    public function addDetailItem()
    {
        $this->detailItems[] = ['name' => '', 'value' => ''];
    }

    public function removeDetailItem($index)
    {
        unset($this->detailItems[$index]);
        $this->detailItems = array_values($this->detailItems);

        // Jangan sampai kosong total, minimal 1 baris tersedia untuk diisi
        if (empty($this->detailItems)) {
            $this->detailItems = [['name' => '', 'value' => '']];
        }
    }

    public function updatedSearch()
    {
        // Dropdown sudah terbuka lewat klik, di sini tinggal memfilter isinya.
        $this->isOpen = true;
        $this->loadResults($this->search);
    }

    public function updatedCategoryId($value)
    {
        $details = Detail::where('category_id', $value)
            ->orderBy('name')
            ->get();

        if ($details->isEmpty()) {
            $this->detailItems = [
                [
                    'id' => null,
                    'name' => '',
                    'value' => '',
                ]
            ];

            return;
        }

        $this->detailItems = $details->map(function ($detail) {
            return [
                'id' => $detail->id,
                'name' => ucwords($detail->name),
                'value' => '',
            ];
        })->toArray();
    }

    /**
     * Query bersama untuk daftar default (keyword kosong)
     * maupun hasil pencarian (keyword terisi).
     */
    protected function loadResults(string $keyword = '')
    {
        $keyword = trim($keyword);

        $this->results = ComponentModel::select('id_component', 'name_component')
            ->when($keyword !== '', fn($q) => $q->where('name_component', 'like', '%' . $keyword . '%'))
            ->when(!empty($this->components), fn($q) => $q->whereNotIn('id_component', $this->components))
            ->orderBy('name_component')
            ->limit($this->componentLimit)
            ->get();
    }

    public function selectComponent($id)
    {
        if (!in_array($id, $this->components)) {
            $this->components[] = $id;
        }

        $this->search = '';
        $this->results = collect();
        $this->isOpen = false;
    }

    public function removeComponent($id)
    {
        $this->components = array_values(array_filter($this->components, fn($item) => $item !== $id));
    }

    /**
     * Ambil detail komponen yang sudah dipilih dalam satu query,
     * dipakai di blade untuk menampilkan badge (menghindari N+1).
     */
    public function getSelectedComponentsProperty()
    {
        if (empty($this->components)) {
            return collect();
        }

        return ComponentModel::select('id_component', 'name_component')
            ->whereIn('id_component', $this->components)
            ->get();
    }

    public function closeDropdown()
    {
        $this->isOpen = false;
    }

    public function generateAssetCodeAndPurchaseDate()
    {
        // Format date
        $this->purchase_date = date('Y-m-d');

        // Format asset code
        $lastAsset = Asset::orderBy('asset_code', 'desc')->first();
        if (!$lastAsset) {
            $this->asset_code = 'AP-' . str_pad('1', 6, '0', STR_PAD_LEFT);
        } else {
            $assetCode = explode('-', $lastAsset->asset_code);
            $number = intval(end($assetCode)) + 1;
            $this->asset_code = 'AP-' . str_pad($number, 6, '0', STR_PAD_LEFT);
        }
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    public function store(ImageService $imageService)
    {
        $this->validate([
            'asset_code' => 'required',
            'image' => ['nullable', 'image', 'max:2048'],
            'name' => 'required',
            'category_id' => 'required',
            'purchase_date' => 'required',
            // locations
            'location_id' => 'required',
            'details' => 'required',
            'moved_at' => 'required',
        ]);

        $imagePath = null;

        if ($this->image) {
            $imagePath = $imageService->uploadAssetImage(
                file: $this->image,
                assetCode: $this->asset_code,
            );
        }

        $asset = Asset::create([
            'asset_code' => $this->asset_code,
            'image_path' => $imagePath,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'purchase_date' => $this->purchase_date,
        ]);

        $asset->components()->sync($this->components);

        $asset->assets_histories()->create([
            'location_id' => $this->location_id,
            'details' => $this->details,
            'moved_at' => $this->moved_at,
        ]);

        foreach ($this->detailItems as $item) {

            if (blank($item['name']) || blank($item['value'])) {
                continue;
            }

            $name = strtolower(preg_replace('/\s+/', ' ', trim($item['name'])));

            $detail = Detail::firstOrCreate([
                'name' => $name,
                'category_id' => $this->category_id
            ]);

            $asset->details()->syncWithoutDetaching([
                $detail->id => [
                    'value' => trim($item['value']),
                ]
            ]);
        }

        AuditService::log(
            AuditEvent::ASSET_CREATED,
            'asset',
            $asset,
            [
                'asset_code' => $this->asset_code,
                'name' => $this->name,
            ]
        );

        return redirect()->to('/assets')->with('message', 'Asset created successfully.');
    }

    public function resetFieldImage()
    {
        $this->image = null;
    }

    public function render()
    {
        return view('livewire.asset.asset-create');
    }
}
