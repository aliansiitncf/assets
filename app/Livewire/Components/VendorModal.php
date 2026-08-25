<?php

namespace App\Livewire\Components;

use App\Enums\AuditEvent;
use App\Models\Vendor as VendorModel;
use App\Services\AuditService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

// OpenVendorModal(jenis) => dipanggil dari asset-repair.blade.php 

#[Layout('components.layouts.app')]
class VendorModal extends Component
{
    public $vendorId = null;
    public $name, $phone, $address;

    public $is_supplier = false;
    public $is_service = false;

    public $isOpen = false;

    protected $listeners = [
        'openVendorModal' => 'openModal',
        'editVendorModal' => 'editModal',
    ];

    public function resetInputFields()
    {
        $this->name = '';
        $this->phone = '';
        $this->address = '';
        $this->is_supplier = false;
        $this->is_service = false;
        $this->vendorId = null;
    }

    public function openModal($jenis = null)
    {
        $this->resetInputFields();
        $this->is_supplier = $jenis === 'supplier';
        $this->is_service = $jenis === 'teknisi';
        $this->isOpen = true;
    }

    public function editModal($id)
    {
        $vendor = VendorModel::findOrFail($id);
        $this->vendorId = $id;
        $this->name = $vendor->name;
        $this->phone = $vendor->phone;
        $this->address = $vendor->address;
        $this->is_supplier = $vendor->is_supplier;
        $this->is_service = $vendor->is_service;
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3|unique:vendors,name,' . ($this->vendorId ?: 'NULL') . ',id',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|numeric',
            'is_supplier' => 'required_without:is_service',
            'is_service' => 'required_without:is_supplier',
        ], [
            'is_supplier.required_without' => 'Pilih minimal satu jenis vendor.',
            'is_service.required_without' => 'Pilih minimal satu jenis vendor.',
        ]);


        $isEdit = $this->vendorId ? true : false;

        $oldVendorModel = $isEdit ? VendorModel::find($this->vendorId) : null;
        $oldVendorData = $oldVendorModel ? [
            'name'    => $oldVendorModel->name,
            'address' => $oldVendorModel->address,
            'phone'   => $oldVendorModel->phone,
            'is_supplier' => $oldVendorModel->is_supplier,
            'is_service' => $oldVendorModel->is_service,
        ] : null;

        $vendor =   VendorModel::updateOrCreate(
            ['id' => $this->vendorId],
            [
                'name'    => $this->name,
                'address' => $this->address,
                'phone'   => $this->phone,
                'is_supplier' => $this->is_supplier,
                'is_service' => $this->is_service,
            ]
        );

        if ($this->vendorId) {
            $changes = [];
            foreach ($oldVendorData as $field => $oldValue) {
                $newValue = $vendor->{$field};
                if ($oldValue != $newValue) {
                    $changes[$field] = [
                        'before' => $oldValue,
                        'after'  => $newValue,
                    ];
                }
            }

            $message = 'Data vendor berhasil diperbarui.';

            AuditService::log(
                AuditEvent::VENDOR_UPDATED,
                'vendor_updated',
                $vendor,
                ['changes' => $changes]
            );
        } else {
            $message = 'Data vendor berhasil ditambahkan.';

            AuditService::log(
                AuditEvent::VENDOR_CREATED,
                'vendor_created',
                $vendor,
                ['name' => $this->name]
            );
        }

        if ($this->is_supplier && $this->is_service) {
            $this->dispatch('vendor-saved', isSupplier: true, vendorId: $vendor->id);
            $this->dispatch('technician-saved', isService: true, technicianId: $vendor->id);
        } elseif ($this->is_supplier) {
            $this->dispatch('vendor-saved', isSupplier: true, vendorId: $vendor->id);
        } elseif ($this->is_service) {
            $this->dispatch('technician-saved', isService: true, technicianId: $vendor->id);
        }

        $this->isOpen = false;

        $this->dispatch('swal', icon: 'success', title: 'Berhasil', text: $message);
        $this->resetInputFields();
    }

    public function render()
    {
        return view('livewire.components.vendor-modal');
    }
}
