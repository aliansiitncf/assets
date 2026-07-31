<?php

namespace App\Livewire\Components;

use App\Models\Technician as TechnicianModel;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ModalTechnician extends Component
{
    public $technicianId = null;
    public $name, $phone;
    public $isOpen = false;
    public $message, $messageType = 'success';

    protected $listeners = [
        'openTechnicianModal' => 'openModal',
        'editTechnicianModal' => 'editModal',
    ];

    public function resetInputFields()
    {
        $this->name = '';
        $this->phone = '';
        $this->technicianId = null;
        $this->message = null;
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function editModal($id)
    {
        $technician = TechnicianModel::findOrFail($id);
        $this->technicianId = $id;
        $this->name = $technician->name;
        $this->phone = $technician->phone;
        $this->message = null;
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3|unique:technicians,name,' . $this->technicianId . ',id',
            'phone' => 'nullable|numeric'
        ]);

        if ($this->technicianId) {
            $technician = TechnicianModel::find($this->technicianId);
            $technician->update([
                'name' => $this->name,
                'phone' => $this->phone
            ]);
        } else {
            $technician = TechnicianModel::create([
                'name' => $this->name,
                'phone' => $this->phone
            ]);
        }
        $this->isOpen = false;

        $message = $this->technicianId
            ? 'Data teknisi berhasil diperbarui.'
            : 'Data teknisi berhasil ditambah.';

        $this->dispatch('swal', icon: 'success', title: 'Berhasil', text: $message);
        $this->dispatch('technician-saved', technicianId: $technician->id);
        $this->resetInputFields();
    }

    public function render()
    {
        return view('livewire.components.modal-technician');
    }
}
