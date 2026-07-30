<?php

namespace App\Livewire\Vendor;

use App\Models\Vendor;
use App\Traits\HasAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;


#[Title('Vendor')]
#[Layout('components.layouts.app')]
class PageVendor extends Component
{
    use WithPagination, HasAuthorization;

    public $showModal = false;
    public $modalMode = 'create';
    public $name, $address, $phone, $vendorId;
    public $updateMode = false;
    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    private function getQuery()
    {
        return Vendor::query()
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
        $this->address = '';
        $this->phone = '';
        $this->vendorId = null;
        $this->updateMode = false;
    }

    // --------------------------------------------

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3|unique:vendors,name,' . $this->vendorId . ',id',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|numeric'
        ]);

        Vendor::updateOrCreate(
            ['id' => $this->vendorId],
            [
                'name' => $this->name,
                'address' => $this->address,
                'phone' => $this->phone,
            ]
        );

        $message = $this->modalMode === 'edit'
            ? 'Data vendor berhasil diperbarui.'
            : 'Data vendor berhasil ditambah.';

        $this->resetInputFields();
        $this->closeModal();

        $this->dispatch('swal', icon: 'success', title: 'Berhasil', text: $message);
    }


    // edit category
    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        $this->vendorId = $id;
        $this->name = $vendor->name;
        $this->address = $vendor->address;
        $this->phone = $vendor->phone;
        $this->updateMode = true;
    }

    public function delete($id)
    {
        $this->requirePermission('hapus kategori');
        $vendor = Vendor::find($id);
        $vendor->delete();
        $this->resetPageIfEmpty();
        $this->dispatch('swal', icon: 'success', title: 'Berhasil', text: 'Data vendor berhasil dihapus.');
    }

    public function render()
    {
        $arr['vendors'] = $this->getQuery();
        return view('livewire.vendor.page-vendor', $arr);
    }
}
