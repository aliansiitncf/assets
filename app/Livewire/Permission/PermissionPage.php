<?php

namespace App\Livewire\Permission;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
#[Title('Permission Management')]
class PermissionPage extends Component
{
    use WithPagination;

    public $showModal = false;
    public $modalMode = 'create';
    public $name;
    public $permissionId;

    public $search = '';
    public $perPage = 10;

    protected function rules() {
        return [
            'name' => 'required|unique:permissions,name'.($this->permissionId ? ','.$this->permissionId : ''),
        ];
    }

    public function mount()
    {
        if (!auth()->user()->hasRole('administrator')) {
            abort(403, 'Hanya Administrator yang dapat mengakses halaman ini.');
        }
    }
    public function updatedSearch() { $this->resetPage(); }

    public function openModal($mode='create', $id=null)
    {
        $this->resetInputFields();
        $this->modalMode = $mode;

        if ($mode==='edit' && $id){
            $perm = Permission::findOrFail($id);
            $this->permissionId = $perm->id;
            $this->name = $perm->name;
        }

        $this->showModal = true;
    }

    public function closeModal() { $this->showModal=false; }

    public function resetInputFields()
    {
        $this->name = '';
        $this->permissionId = null;
    }

    public function store()
    {
        $this->validate();
        Permission::create(['name'=>$this->name]);
        session()->flash('message','Permission created successfully.');
        $this->resetInputFields();
        $this->closeModal();
    }

    public function update()
    {
        $this->validate();
        $perm = Permission::findOrFail($this->permissionId);
        $perm->update(['name'=>$this->name]);
        session()->flash('message','Permission updated successfully.');
        $this->resetInputFields();
        $this->closeModal();
    }

    public function delete($id)
    {
        Permission::find($id)->delete();
        session()->flash('message','Permission deleted successfully.');
    }

    public function render()
    {
        $query = Permission::query();
        if ($this->search) {
            $query->where('name','like',"%{$this->search}%");
        }
        $permissions = $query->latest()->paginate($this->perPage);
        return view('livewire.permission.permission-page', compact('permissions'));
    }
}
