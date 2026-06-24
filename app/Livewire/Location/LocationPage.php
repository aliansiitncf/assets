<?php

namespace App\Livewire\Location;

use App\Enums\AuditEvent;
use App\Models\Location;
use App\Services\AuditService;
use App\Traits\HasAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Location')]
#[Layout('components.layouts.app')]
class LocationPage extends Component
{
    use WithPagination, HasAuthorization;
    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $name, $locationId;
    public $updateMode = false;
    public $search = '';
    public $perPage = 5;
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function mount()
    {
        $this->requirePermission('lihat lokasi');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
    private function getQuery()
    {
        return Location::where('name', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
             ->paginate($this->perPage);
    }
    private function resetPageIfEmpty()
    {
        if ($this->getQuery()->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
        }
    }

    public function openModal($mode = 'create', $id = null)
    {
        $this->requirePermission($mode === 'edit' ? 'ubah lokasi' : 'tambah lokasi');
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
        $this->locationId = null;
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3|unique:locations,name,' . $this->locationId  . ',id_location',
        ]);
        $oldLocation = Location::find($this->locationId);
        $location = Location::updateOrCreate(
            ['id_location' => $this->locationId],
            ['name' => $this->name]
        );
        session()->flash('message', $this->locationId ? 'Location updated successfully.' : 'Location created successfully.');
        if ($this->modalMode === 'edit' && $oldLocation) {
            $auditData = [
                'name' => [
                    'before' => $oldLocation->name,
                    'after' => $location->name,
                ],
            ];
            AuditService::log(
                AuditEvent::LOCATION_UPDATED,
                'location_updated',
                $location,
                ['changes' => $auditData]
            );
        } else {
            AuditService::log(
                AuditEvent::LOCATION_CREATED,
                'location',
                $location,
                [
                    'name' => $this->name,
                ]
            );
        }
        $this->resetInputFields();
        $this->closeModal();
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        $this->locationId = $id;
        $this->name = $location->name;
        $this->updateMode = true;
    }

    public function delete($id)
    {
        $this->requirePermission('hapus lokasi');
        $location = Location::findOrFail($id);
        $location->delete();
        AuditService::log(
            AuditEvent::LOCATION_DELETED,
            'location',
            $location,
            [
                'name' => $location->name,
            ]
        );
        session()->flash('message', 'Location deleted successfully.');
        $this->resetPageIfEmpty();
    }

    public function render()
    {
        $locations = $this->getQuery();
        return view('livewire.location.location-page', compact('locations'));
    }
}
