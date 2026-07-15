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
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

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

    // Detail tambahan (tab Detail): array of ['name' => '', 'value' => '']
    public array $detailItems = [];

    // status buka/tutup dropdown, di-entangle dengan Alpine di blade
    public bool $isOpen = false;

    // jumlah maksimal opsi yang ditampilkan sekali render
    protected int $componentLimit = 10;

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
        $this->detailItems = $asset->details
            ->map(fn($detail) => [
                'id' => $detail->id,
                'name' => $detail->name,
                'value' => $detail->pivot->value
            ])
            ->toArray();
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

    public function selectComponent($id)
    {
        if (!in_array($id, $this->components)) {
            $this->components[] = $id;
        }

        $this->search = '';
        $this->results = [];
        $this->isOpen = false;
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

        // 1. Simpan kondisi detail tambahan sebelum diubah, untuk dibandingkan setelahnya
        $oldDetails = $this->asset->details
            ->mapWithKeys(fn($detail) => [
                $detail->id => [
                    'name' => $detail->name,
                    'value' => $detail->pivot->value,
                ]
            ])
            ->toArray();

        // Siapkan data untuk sync
        $syncData = [];

        // 3. Simpan detail baru dari $this->detailItems
        foreach ($this->detailItems as $item) {

            if (blank($item['name']) || blank($item['value'])) {
                continue;
            }

            $name = strtolower(preg_replace('/\s+/', ' ', trim($item['name'])));

            if (!empty($item['id'])) {

                $detail = Detail::find($item['id']);

                if ($detail) {
                    $detail->update([
                        'name' => $name,
                        'category_id' => $this->category_id,
                    ]);
                } else {
                    $detail = Detail::create([
                        'name' => $name,
                        'category_id' => $this->category_id,
                    ]);
                }
            } else {

                $detail = Detail::firstOrCreate([
                    'name' => $name,
                    'category_id' => $this->category_id,
                ]);
            }

            $syncData[$detail->id] = [
                'value' => trim($item['value']),
            ];
        }

        // Replace seluruh detail asset
        $this->asset->details()->sync($syncData);


        // Ambil detail terbaru untuk audit
        $newDetails = $this->asset->fresh()->details
            ->mapWithKeys(fn($detail) => [
                $detail->id => [
                    'name' => $detail->name,
                    'value' => $detail->pivot->value,
                ]
            ])
            ->toArray();


        // Log Detail Tambahan Updated
        $changes = [];

        $ids = array_unique(array_merge(
            array_keys($oldDetails),
            array_keys($newDetails)
        ));

        foreach ($ids as $id) {

            $before = $oldDetails[$id] ?? null;
            $after = $newDetails[$id] ?? null;

            if ($before != $after) {

                $changes[] = [
                    'detail_id' => $id,
                    'before' => $before,
                    'after' => $after,
                ];
            }
        }

        if (!empty($changes)) {

            AuditService::log(
                AuditEvent::ASSET_DETAIL_UPDATED,
                'asset_detail_updated',
                $this->asset,
                [
                    'changes' => $changes,
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
