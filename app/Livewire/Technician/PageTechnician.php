<?php

namespace App\Livewire\Technician;

use App\Models\Technician;
use App\Traits\HasAuthorization;
use Livewire\Component;
use Livewire\WithPagination;

class PageTechnician extends Component
{
    use WithPagination, HasAuthorization;

    public $showModal = false;
    public $modalMode = 'create';
    public $name, $phone, $technicianId;
    public $updateMode = false;
    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    private function getQuery()
    {
        return Technician::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    private function resetPageIfEmpty()
    {
        if ($this->getQuery()->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openModal($mode = 'create', $id = null)
    {
        $this->resetInputFields();
        $this->modalMode = $mode;
        if ($mode === 'edit' && $id) {
            $this->edit($id);
        }
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->phone = '';
        $this->technicianId = null;
        $this->updateMode = false;
    }

    // --------------------------------------------

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3|unique:technicians,name,' . $this->technicianId . ',id',
            'phone' => 'nullable|numeric'
        ]);

        Technician::updateOrCreate(
            ['id' => $this->technicianId],
            [
                'name' => $this->name,
                'phone' => $this->phone,
            ]
        );

        $message = $this->modalMode === 'edit'
            ? 'Data teknisi berhasil diperbarui.'
            : 'Data teknisi berhasil ditambah.';


        $this->resetInputFields();
        $this->closeModal();

        $this->dispatch('swal', icon: 'success', title: 'Berhasil', text: $message);
    }


    // edit category
    public function edit($id)
    {
        $technician = Technician::findOrFail($id);
        $this->technicianId = $id;
        $this->name = $technician->name;
        $this->phone = $technician->phone;
        $this->updateMode = true;
    }

    public function delete($id)
    {
        $this->requirePermission('hapus kategori');
        $technician = technician::find($id);
        $technician->delete();
        $this->resetPageIfEmpty();
        $this->dispatch('swal', icon: 'success', title: 'Berhasil', text: 'Data teknisi berhasil dihapus.');
    }

    public function render()
    {
        $arr['technicians'] =  $this->getQuery();
        return view('livewire.technician.page-technician', $arr);
    }
}
