<?php

namespace App\Livewire\Asset;

use App\Enums\AuditEvent;
use App\Models\Asset;
use App\Models\AssetComponent;
use App\Models\AssetRepair as AssetRepairModel;
use App\Models\Component as ComponentModel;
use App\Services\AuditService;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit Perbaikan')]
#[Layout('components.layouts.app')]
class AssetRepairEdit extends Component
{
    public $repairImage = null;
    public $existingImagePath = null;
    public $components = [];
    public $repairComponents = [];

    public $asset;
    public $repair;
    public $repairId;
    public $repairAssetId;

    public $selectedComponent = '';
    public $merk = '';
    public $dateInstal = '';
    public $toko = '';
    public $technician = '';
    public $qty = 1;
    public $harga = 0;
    public $repairNotes = '';
    public $out_of_service = '';
    public $in_of_service = '';
    public $poin = '';
    public $hmkm = '';

    public $isOpen = false;
    public $name = '';
    public $name_component = '';
    public $componentId = null;

    public function mount(AssetRepairModel $assetRepair)
    {
        // Muat data perbaikan beserta relasi komponennya
        $this->repair = $assetRepair->load(['components', 'asset']);
        $this->repairId = $assetRepair->id_asset_repair; // sesuaikan jika nama primary key berbeda

        $this->asset = $this->repair->asset->load('components');
        $this->repairAssetId = $this->asset->id_asset;
        $this->name = $this->asset->name;
        $this->components = $this->asset->components;

        // Prefill form utama dari data yang sudah ada
        $this->repairNotes = $this->repair->repair_note;
        $this->existingImagePath = $this->repair->image_path;
        $this->out_of_service = optional($this->repair->started_at)->format('Y-m-d');
        $this->in_of_service = optional($this->repair->completed_at)->format('Y-m-d');
        $this->poin = $this->repair->poin;
        $this->hmkm = $this->repair->hm_km;

        // Prefill daftar komponen dari pivot table
        $this->repairComponents = $this->repair->components->map(function ($component) {
            return [
                'component_id'   => $component->id_component,
                'name_component' => $component->name_component,
                'merk'           => $component->pivot->merk,
                'qty'            => $component->pivot->qty,
                'harga'          => $component->pivot->price,
                'dateInstal'     => $component->pivot->date,
                'technician'     => $component->pivot->technician,
                'store'          => $component->pivot->store,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.asset.asset-repair-edit');
    }

    public function update(ImageService $imageService)
    {
        $this->validate([
            'repairNotes' => 'required|string',
            'out_of_service' => 'required|date',
            'in_of_service' => 'nullable|date',
            'poin' => 'required|integer|min:0',
            'hmkm' => 'required|numeric|min:0',
            'repairImage' => 'nullable|image|max:2048',
            'repairComponents' => 'required|array|min:1',
            'repairComponents.*.component_id' => 'required|exists:components,id_component',
            'repairComponents.*.name_component' => 'required|string',
            'repairComponents.*.merk' => 'nullable|string',
            'repairComponents.*.qty' => 'required|integer|min:1',
            'repairComponents.*.harga' => 'required|numeric|min:0',
            'repairComponents.*.dateInstal' => 'nullable|date',
            'repairComponents.*.technician' => 'nullable|string|min:1',
            'repairComponents.*.store' => 'nullable|string',
        ], [
            'repairComponents.required' => 'Tambahkan minimal satu komponen perbaikan.',
            'repairComponents.min' => 'Tambahkan minimal satu komponen perbaikan.',
        ]);

        try {
            DB::transaction(function () use ($imageService) {
                $imagePath = $this->existingImagePath;

                // Ganti gambar hanya jika user upload gambar baru
                if ($this->repairImage) {
                    if ($this->existingImagePath) {
                        $imageService->deleteImage($this->existingImagePath);
                    }
                    $imagePath = $imageService->uploadRepairAssetImage(
                        file: $this->repairImage,
                        repairAssetId: 'Repair_' . $this->repairAssetId . '_' . time()
                    );
                }

                // Snapshot data lama sebelum diupdate, untuk dibandingkan
                $before = $this->repair->only([
                    'repair_note',
                    'started_at',
                    'completed_at',
                    'hm_km',
                    'poin',
                    'status',
                ]);

                // Snapshot komponen lama (per component_id) sebelum di-sync
                $oldComponents = $this->repair->components()
                    ->get()
                    ->mapWithKeys(fn($c) => [
                        $c->id_component => [
                            'merk'       => $c->pivot->merk,
                            'qty'        => (int) $c->pivot->qty,
                            'price'      =>
                            (float) $c->pivot->price == (int) $c->pivot->price
                                ? (int) $c->pivot->price
                                : (float) $c->pivot->price,
                            'date'       => $c->pivot->date,
                            'technician' => $c->pivot->technician,
                            'store'      => $c->pivot->store,
                            'subtotal'   =>
                            (float) $c->pivot->subtotal == (int) $c->pivot->subtotal
                                ? (int) $c->pivot->subtotal
                                : (float) $c->pivot->subtotal,
                        ],
                    ])
                    ->toArray();

                // Update data perbaikan utama
                $this->repair->update([
                    'repair_note'  => $this->repairNotes,
                    'image_path'   => $imagePath,
                    'started_at'   => $this->out_of_service,
                    'completed_at' => $this->in_of_service ?: null,
                    'hm_km'        => $this->hmkm,
                    'poin'         => $this->poin,
                    'status'       => $this->in_of_service ? 'Completed' : 'In Progress',
                ]);

                // Hanya kolom yang benar-benar berubah setelah update()
                $changedFields = $this->repair->getChanges();

                $auditChanges = [];
                foreach ($changedFields as $key => $value) {
                    // updated_at ikut berubah tiap kali update, tidak perlu dicatat
                    if ($key === 'updated_at') {
                        continue;
                    }

                    $auditChanges[$key] = [
                        'before' => $before[$key] ?? null,
                        'after' => $value,
                    ];
                }

                // Sinkronkan komponen: yang dihapus dari form akan ikut
                // terhapus dari pivot, yang baru akan ditambahkan/diupdate
                $syncData = [];
                foreach ($this->repairComponents as $item) {
                    $syncData[$item['component_id']] = [
                        'merk'       => $item['merk'],
                        'qty'        => $item['qty'],
                        'price'      => $item['harga'],
                        'date'       => $item['dateInstal'] ?? null,
                        'technician' => $item['technician'] ?? null,
                        'store'      => $item['store'] ?? null,
                        'subtotal'   => $item['subtotal'] ?? ($item['qty'] * $item['harga']),
                    ];
                }
                $syncResult = $this->repair->components()->sync($syncData);

                $componentNames = ComponentModel::whereIn('id_component', array_merge(
                    $syncResult['attached'],
                    $syncResult['detached'],
                    $syncResult['updated']
                ))->pluck('name_component', 'id_component');

                $auditComponents = [];

                if (!empty($syncResult['attached'])) {
                    $auditComponents['added'] = collect($syncResult['attached'])
                        ->map(fn($id) => $componentNames[$id] ?? $id)
                        ->values()
                        ->all();
                }

                if (!empty($syncResult['detached'])) {
                    $auditComponents['removed'] = collect($syncResult['detached'])
                        ->map(fn($id) => $componentNames[$id] ?? $id)
                        ->values()
                        ->all();
                }

                $updatedComponents = [];

                foreach ($syncResult['updated'] as $id) {

                    $before = $this->normalizeComponent($oldComponents[$id] ?? []);
                    $after  = $this->normalizeComponent($syncData[$id] ?? []);

                    // Skip jika tidak ada perubahan
                    if ($before == $after) {
                        continue;
                    }

                    $updatedComponents[] = [
                        'component' => $componentNames[$id] ?? $id,
                        'before'    => $before,
                        'after'     => $after,
                    ];
                }

                if (!empty($updatedComponents)) {
                    $auditComponents['updated'] = $updatedComponents;
                }

                $newCondition = $this->in_of_service ? 'Baik' : 'Perbaikan';

                // Hanya update kondisi asset kalau memang berubah
                if ($this->asset->condition !== $newCondition) {
                    Asset::where('id_asset', $this->repairAssetId)->update([
                        'condition' => $newCondition,
                    ]);
                }

                // Catat audit log hanya jika ada perubahan field atau komponen
                if (!empty($auditChanges) || !empty($auditComponents)) {
                    $logPayload = [
                        'asset_code' => $this->asset->asset_code,
                        'name' => $this->asset->name,
                    ];

                    if (!empty($auditChanges)) {
                        $logPayload['changes'] = $auditChanges;
                    }

                    if (!empty($auditComponents)) {
                        $logPayload['components'] = $auditComponents;
                    }

                    AuditService::log(
                        AuditEvent::ASSET_REPAIR_UPDATED,
                        'asset_repair',
                        $this->asset,
                        $logPayload
                    );
                }
            });

            $this->reset(['repairImage']);
            session()->flash('message', 'Data perbaikan berhasil diperbarui.');
        } catch (\Throwable $e) {
            report($e);
            $this->addError('repairNotes', 'Terjadi kesalahan saat memperbarui data perbaikan. Silakan coba lagi.');
        }
    }

    public function openModalComponent()
    {
        $this->isOpen = true;
    }

    // simpan data komponen baru (sama seperti AssetRepair)
    public function save()
    {
        $this->validate([
            'name_component' => 'required|string|min:1',
        ]);

        $component = ComponentModel::create([
            'name_component' => $this->name_component,
        ]);

        AssetComponent::create([
            'asset_id'     => $this->repairAssetId,
            'component_id' => $component->id_component,
        ]);

        $this->components = $this->asset->components()->get();

        $this->reset(['name_component', 'componentId']);
        $this->isOpen = false;
    }

    public function addComponentItem()
    {
        $this->validate([
            'selectedComponent' => 'required|exists:components,id_component',
            'merk' => 'required|string|min:1',
            'qty' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'dateInstal' => 'nullable|date',
            'technician' => 'nullable|string|min:1',
        ]);

        $component = ComponentModel::find($this->selectedComponent);

        if ($component) {
            $this->repairComponents[] = [
                'component_id'   => $component->id_component,
                'name_component' => $component->name_component,
                'merk'           => $this->merk,
                'qty'            => $this->qty,
                'harga'          => $this->harga,
                'dateInstal'     => $this->dateInstal,
                'technician'     => $this->technician,
                'store'           => $this->toko,
            ];

            $this->reset(['selectedComponent', 'merk', 'qty', 'harga', 'dateInstal', 'technician', 'toko']);
        }
    }

    public function removeComponentItem($index)
    {
        unset($this->repairComponents[$index]);
        $this->repairComponents = array_values($this->repairComponents);
    }

    public function resetImageRepair()
    {
        $this->repairImage = null;
    }

    public function closeRepairModal()
    {
        $this->isOpen = false;
    }

    private function normalizeComponent(array $component): array
    {
        return [
            'merk'       => $component['merk'] ?? null,
            'qty'        => (int) ($component['qty'] ?? 0),
            'price'      => (int) ($component['price'] ?? 0),
            'date'       => $component['date'] ?? '',
            'technician' => $component['technician'] ?? '',
            'store'      => $component['store'] ?? null,
            'subtotal'   => (int) ($component['subtotal'] ?? 0),
        ];
    }
}
