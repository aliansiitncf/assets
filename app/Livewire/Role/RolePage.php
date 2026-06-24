<?php

namespace App\Livewire\Role;

use App\Traits\HasAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
#[Title('Role Management')]

class RolePage extends Component
{
    use WithPagination, HasAuthorization;

    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $name, $permissions = [], $roleId;

    public $search = '';
    public $perPage = 5;
    public $allPermissions;

    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            // toggle arah sorting (asc <-> desc)
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // ganti kolom sorting dan reset arah
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|unique:roles,name' . ($this->roleId ? ',' . $this->roleId : ''),
        ];
    }

    public function mount()
    {
        $this->requireRole('administrator');
        $this->allPermissions = Permission::all();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    private function resetPageIfEmpty()
    {
        if ($this->getQuery()->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
        }
    }
    private function getQuery()
    {
        return Role::with('permissions')->when($this->search, function ($query) {
            $query->where('name', 'like', "%{$this->search}%");
        })->orderBy($this->sortField, $this->sortDirection);
    }
    public function openModal($mode = 'create', $id = null)
    {
        $this->resetInputFields();
        $this->modalMode = $mode;

        if ($mode === 'edit' && $id) {
            $role = Role::findOrFail($id);
            $this->roleId = $role->id;
            $this->name = $role->name;
            $this->permissions = $role->permissions->pluck('id')->toArray();
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
        $this->permissions = [];
        $this->roleId = null;
    }

    public function store()
    {
        $this->validate();

        $role = Role::create(['name' => $this->name]);
        $role->syncPermissions(Permission::whereIn('id', $this->permissions)->get());

        session()->flash('message', 'Role created successfully.');
        $this->resetInputFields();
        $this->closeModal();
    }

    public function update()
    {
        $this->validate();

        $role = Role::findOrFail($this->roleId);
        $role->update(['name' => $this->name]);
        $role->syncPermissions(Permission::whereIn('id', $this->permissions)->get());

        session()->flash('message', 'Role updated successfully.');
        $this->resetInputFields();
        $this->closeModal();
    }

    public function delete($id)
    {
        Role::find($id)->delete();
        session()->flash('message', 'Role deleted successfully.');
        $this->resetPageIfEmpty();
    }

    public function groupedPermissions()
    {
        return $this->allPermissions->groupBy(function ($permission) {
            $text = explode(' ', $permission->name);
            return end($text);
        });
    }

    public function render()
    {
        $roles = $this->getQuery()->paginate($this->perPage);

        return view('livewire.role.role-page', compact('roles'));
    }
}
