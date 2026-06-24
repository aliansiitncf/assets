<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Traits\HasDataTable;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
#[Title('User Management')]
class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $name, $email, $password, $userId, $role_id;
    public $updateMode = false;
    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 5;


    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role_id' => 'required',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    private function getQuery()
    {
        return User::with('roles')->when($this->search, function ($query) {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%");
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage);
    }
    private function resetPageIfEmpty()
    {
        if ($this->getQuery()->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
        }
    }

    public function mount()
    {
        if (!auth()->user()->hasRole('administrator')) {
            abort(403, 'Hanya Administrator yang dapat mengakses halaman ini.');
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
        $this->email = '';
        $this->password = '';
        $this->role_id = '';
        $this->userId = null;
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role_id' => $this->role_id,
        ]);
        $roleName = Role::findOrFail($this->role_id)->name;
        $user->assignRole($roleName);

        session()->flash('message', 'User created successfully.');
        $this->resetInputFields();
        $this->closeModal();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->userId . ',id_user',
            'password' => 'nullable|min:6',
            'role_id' => 'nullable',
        ]);

        $user = User::findOrFail($this->userId);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password ? Hash::make($this->password) : $user->password,
            'role_id' => $this->role_id,
        ]);

        if ($this->role_id) {
            $roleName = Role::findOrFail($this->role_id)->name;
            $user->syncRoles($roleName);
        }
        session()->flash('message', 'User updated successfully.');
        $this->resetInputFields();
        $this->closeModal();
    }

    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'User deleted successfully.');
        $this->resetPageIfEmpty();
    }

    public function render()
    {
        $users = $this->getQuery();
        $roles = Role::all();
        return view('livewire.users.index', compact('users', 'roles'));
    }
}
