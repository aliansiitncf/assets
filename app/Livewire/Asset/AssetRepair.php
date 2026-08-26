<?php

namespace App\Livewire\Asset;

use App\Enums\AuditEvent;
use App\Models\Asset;
use App\Models\AssetComponent;
use App\Models\AssetRepair as AssetRepairModel;
use App\Models\Component as ComponentModel;
use App\Models\Vendor as VendorModel;
use App\Models\Vendor;
use App\Services\AuditService;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Component;

// technician-saved => dipanggil dari ModalVendor.php
// vendor-saved => dipanggil dari ModalVendor.php

#[Title('Asset Repair')]
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
    public $vendor_id; // ID Vendor yg  isSupplier = true    
    public $technician_id; // ID Vendor yg is_service = true
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

    public $vendors = [];
    public $technicians = [];

        // Removed listeners array
        
        


    public function mount(Asset $asset)
    {
        $this->asset = $asset->load('components');
        $this->name = $asset->name;
        $this->repairAssetId = $asset->id_asset;
        $this->components = $this->asset->components;

        // select vendor & teknisi
        $this->vendors = Vendor::where("is_supplier", true)->orderBy('name')->get();
        $this->technicians = Vendor::where("is_service", true)->orderBy('name')->get();
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
            'in_of_service' => 'nullable|date|after_or_equal:out_of_service',
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
            'repairComponents.*.technician_id' => 'nullable|exists:vendors,id',
            'repairComponents.*.vendor_id' => 'nullable|exists:vendors,id',
        ], [
            'repairComponents.required' => 'Tambahkan minimal satu komponen perbaikan.',
            'repairComponents.min' => 'Tambahkan minimal satu komponen perbaikan.',
            'in_of_service.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal keluar.',
        ]);

        // 1. Validasi: asset harus berstatus "Rusak"
        $asset = Asset::findOrFail($this->repairAssetId);
        if ($asset->condition !== 'Rusak') {
            $this->addError('repairNotes', 'Asset ini tidak dalam kondisi Rusak. Hanya asset rusak yang dapat diperbaiki.');
            return;
        }

        // 2. Cari asset_damage terakhir yang belum di-link ke repair manapun
        $latestDamage = \App\Models\AssetDamage::where('asset_id', $this->repairAssetId)
            ->whereDoesntHave('repair') // Hanya damage yang belum punya repair
            ->latest('reported_at')
            ->first();

        if (!$latestDamage) {
            $this->addError('repairNotes', 'Tidak ditemukan data kerusakan yang bisa dihubungkan dengan perbaikan ini.');
            return;
        }

        try {
            DB::transaction(function () use ($imageService, $latestDamage) {
                $imagePath = null;
                if ($this->repairImage) {
                    $imagePath = $imageService->uploadRepairAssetImage(
                        file: $this->repairImage,
                        repairAssetId: 'Repair_' . $this->repairAssetId . '_' . time()
                    );
                }

                // 3. Simpan data perbaikan utama + link ke asset_damage
                $repair = AssetRepairModel::create([
                    'asset_id' => $this->repairAssetId,
                    'asset_damage_id' => $latestDamage->id_asset_damage,
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
                        'merk'          => $item['merk'],
                        'qty'           => $item['qty'],
                        'price'         => $item['harga'],
                        'date'          => $item['dateInstal'] ?? null,
                        'technician_id' => $item['technician_id'] ?? null,
                        'vendor_id'     => $item['vendor_id'] ?? null,
                        'subtotal'      => $item['harga'],
                    ]);
                }

                // Update kondisi asset
                $asset = Asset::where('id_asset', $this->repairAssetId);
                $asset->update([
                    'condition' => $this->in_of_service ? 'Baik' : 'Perbaikan',
                ]);

                // Catat audit log
                AuditService::log(
                    AuditEvent::ASSET_REPAIRED,
                    'asset',
                    $this->asset,
                    [
                        'asset_code'      => $this->asset->asset_code,
                        'name'            => $this->asset->name,
                        'repair_note'     => $this->repairNotes,
                        'damage_linked'   => $latestDamage->id_asset_damage,
                        'components'      => $this->repairComponents,
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
                'vendor_id',
                'technician_id'
            ]);
            return redirect()->route('assets')
                ->with('success', 'Data perbaikan berhasil disimpan.');
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
            'merk' => 'nullable|string|min:1',
            'qty' => 'required|integer|min:1',
            'harga' => 'required|integer|min:0',
            'dateInstal' => 'nullable|date',
            'technician_id' => 'required|exists:vendors,id',
            'vendor_id' => 'required|exists:vendors,id'
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
                'subtotal' => $this->harga,
                'teknisi' => VendorModel::find($this->technician_id)?->name ?? null,
                'vendor' => VendorModel::find($this->vendor_id)?->name ?? null,
                'technician_id' => $this->technician_id,
                'vendor_id' => $this->vendor_id,
            ];

            // reset form
            $this->reset(['selectedComponent', 'merk', 'qty', 'harga', 'dateInstal', 'technician_id', 'vendor_id']);
            $this->dispatch('reset-harga');
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

    #[On('technician-saved')]
    public function refreshTechnicianList($isService = false, $technicianId = null)
    {
        // refresh ulang list teknisi dari database
        $this->technicians = VendorModel::where("is_service", $isService)->orderBy('name')->get();

        // langsung pilih teknisi yang baru dibuat/diedit di select
        $this->technician_id = $technicianId;
    }

    #[On('vendor-saved')]
    public function refreshVendorList($isSupplier = false, $vendorId = null)
    {
        $this->vendors = VendorModel::where("is_supplier", $isSupplier)->orderBy("name")->get();
        $this->vendor_id = $vendorId;
    }
}
