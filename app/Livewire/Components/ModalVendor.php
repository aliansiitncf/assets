<?php

namespace App\Livewire\Components;

use App\Models\Vendor as VendorModel;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ModalVendor extends Component
{
    public $vendorId = null;
    public $name, $phone, $address;
    public $isOpen = false;
    public $message, $messageType = 'success';

    protected $listeners = [
        'openVendorModal' => 'openModal',
        'editVendorModal' => 'editModal',
    ];

    public function resetInputFields()
    {
        $this->name = '';
        $this->phone = '';
        $this->address = '';
        $this->vendorId = null;
        $this->message = null;
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function editModal($id)
    {
        $vendor = VendorModel::findOrFail($id);
        $this->vendorId = $id;
        $this->name = $vendor->name;
        $this->phone = $vendor->phone;
        $this->address = $vendor->address;
        $this->message = null;
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3|unique:vendors,name,' . $this->vendorId . ',id',
            'phone' => 'nullable|numeric',
            'address' => 'nullable|string|max:255'
        ]);

        if ($this->vendorId) {
            $vendor = VendorModel::find($this->vendorId);
            $vendor->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'address' => $this->address
            ]);
        } else {
            $vendor = VendorModel::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'address' => $this->address
            ]);
        }
        $this->isOpen = false;

        $message = $this->vendorId
            ? 'Data vendor berhasil diperbarui.'
            : 'Data vendor berhasil ditambah.';

        $this->dispatch('swal', icon: 'success', title: 'Berhasil', text: $message);
        $this->dispatch('vendor-saved', vendorId: $vendor->id);
        $this->resetInputFields();
    }

    public function render()
    {
        return view('livewire.components.modal-vendor');
    }
}
