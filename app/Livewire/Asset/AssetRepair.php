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

#[Title('Components')]
#[Layout('components.layouts.app')]
class AssetRepair extends Component
{
    public $repairImage = null;
    public $components = [];
    public $repairComponents = [];

    public $asset;
    public $repairAssetId;

    public $selectedComponent = '';
    public $merk = '';
    public $dateInstal = '';
    public $toko = '';
    public $technician = '';
    public $qty = 1;
    public $harga = 0;
    public $subtotal = 0;
    public $repairNotes = '';
    public $out_of_service = '';
    public $in_of_service = '';
    public $poin = '';
    public $hmkm = '';

    public $isOpen = false;
    public $name = '';
    public $name_component = '';
    public $componentId = null;

    public function mount(Asset $asset)
    {
        $this->asset = $asset->load('components');
        $this->name = $asset->name;
        $this->repairAssetId = $asset->id_asset;
        $this->components = $this->asset->components;
    }

    public function render()
    {
        return view('livewire.asset.asset-repair');
    }

    public function store(ImageService $imageService)
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
        ], [
            'repairComponents.required' => 'Tambahkan minimal satu komponen perbaikan.',
            'repairComponents.min' => 'Tambahkan minimal satu komponen perbaikan.',
        ]);

        try {
            DB::transaction(function () use ($imageService) {
                $imagePath = null;
                if ($this->repairImage) {
                    $imagePath = $imageService->uploadRepairAssetImage(
                        file: $this->repairImage,
                        repairAssetId: 'Repair_' . $this->repairAssetId . '_' . time()
                    );
                }

                // Simpan data perbaikan utama
                $repair = AssetRepairModel::create([
                    'asset_id' => $this->repairAssetId,
                    'repair_note' => $this->repairNotes,
                    'image_path' => $imagePath,
                    'started_at' => $this->out_of_service,
                    'completed_at' => $this->in_of_service ?: null,
                    'hm_km' => $this->hmkm,
                    'poin' => $this->poin,
                    'status' => $this->in_of_service ? 'Completed' : 'In Progress',
                ]);

                // Simpan detail komponen yang dipakai
                foreach ($this->repairComponents as $item) {
                    $repair->components()->attach($item['component_id'], [
                        'merk'       => $item['merk'],
                        'qty'        => $item['qty'],
                        'price'      => $item['harga'],
                        'date'       => $item['dateInstal'] ?? null,
                        'technician' => $item['technician'] ?? null,
                        'store'     => $item['toko'] ?? null,
                        'subtotal'   => $item['subtotal'] ?? ($item['qty'] * $item['harga']),
                    ]);
                }


                // Update kondisi asset
                Asset::where('id_asset', $this->repairAssetId)->update([
                    'condition' => 'Perbaikan',
                ]);

                // Catat audit log
                AuditService::log(
                    AuditEvent::ASSET_REPAIRED,
                    'asset',
                    $this->asset,
                    [
                        'asset_code'  => $this->asset->asset_code,
                        'name'        => $this->asset->name,
                        'repair_note' => $this->repairNotes,
                        'components'  => $this->repairComponents,
                    ]
                );
            });

            // Reset form & tutup modal setelah sukses
            $this->reset([
                'repairNotes',
                'repairImage',
                'repairComponents',
                'selectedComponent',
                'merk',
                'qty',
                'harga',
                'toko'
            ]);

            session()->flash('message', 'Data perbaikan berhasil disimpan.');
        } catch (\Throwable $e) {
            report($e);
            $this->addError('repairNotes', 'Terjadi kesalahan saat menyimpan data perbaikan. Silakan coba lagi.');
        }
    }

    public function openModalComponent()
    {
        $this->isOpen = true;
    }

    // simpan data komponen baru
    public function save()
    {
        $this->validate([
            'name_component' => 'required|string|min:1',
        ]);

        // mode tambah baru
        $component = ComponentModel::create([
            'name_component' => $this->name_component,
        ]);

        AssetComponent::create([
            'asset_id'     => $this->repairAssetId,
            'component_id' => $component->id_component,
        ]);

        $this->components = $this->asset->components()->get();

        // reset form & tutup modal
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
                'component_id' => $component->id_component,
                'name_component' => $component->name_component,
                'merk' => $this->merk,
                'qty' => $this->qty,
                'harga' => $this->harga,
                'dateInstal' => $this->dateInstal,
                'technician' => $this->technician,
                'subtotal' => $this->qty * $this->harga,
            ];

            // reset form
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
        $this->reset('repairImage');
    }

    public function closeRepairModal()
    {
        $this->isOpen = false;
    }
}
