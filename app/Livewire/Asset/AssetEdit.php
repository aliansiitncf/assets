<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use Livewire\Component;
use App\Models\Category;
use App\Models\Location;
use App\Enums\AuditEvent;
use Livewire\WithFileUploads;
use App\Services\AuditService;
use App\Services\ImageService;
use Livewire\Attributes\Title;
use App\Models\Component as ComponentModel;

#[Title('Edit Asset')]
class AssetEdit extends Component
{
    use WithFileUploads;
    public Asset $asset;
    public $categories;

    public $asset_code, $name, $category_id, $purchase_date, $image, $status;

    // Components
    public $name_component, $components = [];

    public $locationForm;
    // Locations
    public $locations, $location_id, $details, $moved_at;

    public $search = '';
    public $results;
    public function mount($asset)
    {
        $this->asset = $asset;
        $this->asset_code = $asset->asset_code;
        $this->name = $asset->name;
        $this->category_id = $asset->category_id;
        $this->purchase_date = $asset->purchase_date?->format('Y-m-d');
        $this->status = $asset->status;
        $this->locations = Location::select('id_location', 'name')->get();
        $this->categories = Category::select('id_category', 'name')->get();

        $this->components = $asset->components
            ->pluck('id_component')
            ->toArray();

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
        $this->results = [];
    }
    public function removeComponent($id)
    {
        $this->components = array_filter($this->components, fn($item) => $item !== $id);
    }
    public function addLocation()
    {
        $this->locationForm = true;
    }

    public function update(ImageService $imageService)
    {
        $this->validate([
            'name' => 'required',
            'category_id' => 'required',
            'purchase_date' => 'required',
            'image' => 'nullable|image|max:2048',
        ]);
        $before = $this->asset->only([
            'asset_code',
            'name'
        ]);

        $imagePath = $this->asset->image_path;
        if ($this->image) {
            $imagePath = $imageService->uploadAssetImage(
                file: $this->image,
                assetCode: $this->asset_code,
                oldPath: $this->asset->image_path
            );
        }
        $this->asset->update([
            'asset_code' => $this->asset_code,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'purchase_date' => $this->purchase_date,
            'status' => $this->status,
            'image_path' => $imagePath,
        ]);
        $this->asset->components()->sync($this->components);
        $changes = $this->asset->getChanges();
        $allowedChanges = ['name'];
        $filteredChanges = array_intersect_key($changes, array_flip($allowedChanges));
        if (!empty($filteredChanges)) {
            $auditData = [];
            foreach ($filteredChanges as $key => $value) {
                $auditData[$key] = [
                    'before' => $before[$key],
                    'after' => $value,
                ];
            }
            // Log Update Asset
            AuditService::log(
                AuditEvent::ASSET_UPDATED,
                'asset_updated',
                $this->asset,
                ['changes' => $auditData]
            );
        }

        if (!empty($this->location_id) && !empty($this->details) && !empty($this->moved_at)) {
            $this->validate([
                'location_id' => 'required',
                'details' => 'required',
                'moved_at' => 'required|date',
            ]);
            $oldLocation = $this->asset->latestLocation?->location?->name;
            $history = $this->asset->assets_histories()->create([
                'location_id' => $this->location_id,
                'details' => $this->details,
                'moved_at' => $this->moved_at,
            ]);
            // Log Location Moved
            AuditService::log(
                AuditEvent::LOCATION_MOVED,
                'asset moved',
                $this->asset,
                [
                    'from' => $oldLocation,
                    'to' => $history->location->name,
                    'details' => $this->details,
                    'moved_at' => $this->moved_at,
                ]
            );
        }

        // Redirect to assets list with success message
        return redirect()->route('assets')->with('message', 'Asset updated successfully.');
    }

    public function cancel()
    {
        return redirect()->route('assets');
    }
    public function render()
    {
        return view('livewire.asset.asset-edit');
    }
}
