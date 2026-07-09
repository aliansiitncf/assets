<?php

namespace App\Livewire\Asset;

use App\Enums\AuditEvent;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Component as ComponentModel;
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

    public function mount()
    {
        $this->categories = Category::select('id_category', 'name')->get();
        $this->locations = Location::select('id_location', 'name')->get();
        $this->generateAssetCodeAndPurchaseDate();

        $this->results = collect();
    }

    public function updatedSearch()
    {
        if (!$this->search) {
            $this->results = collect();
            return;
        }

        $this->results = ComponentModel::select('id_component', 'name_component')
            ->where('name_component', 'like', '%' . $this->search . '%')
            ->limit(10)
            ->get();
    }

    public function selectComponent($id)
    {
        if (!in_array($id, $this->components)) {
            $this->components[] = $id;
        }

        $this->search = '';
        $this->results = collect();
    }

    public function removeComponent($id)
    {
        $this->components = array_filter($this->components, fn($item) => $item !== $id);
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
