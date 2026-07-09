<?php

namespace App\Livewire\Category;

use App\Enums\AuditEvent;
use App\Models\Category;
use App\Services\AuditService;
use App\Traits\HasAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Category')]
#[Layout('components.layouts.app')]
class PageCategory extends Component
{
    use WithPagination, HasAuthorization;

    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $name, $categoryId;
    public $updateMode = false;
    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    private function getQuery()
    {
        return Category::query()
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
            // toggle arah sorting (asc <-> desc)
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // ganti kolom sorting dan reset arah
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function mount()
    {
        $this->requirePermission('lihat kategori');
    }

    public function openModal($mode = 'create', $id = null)
    {
        $this->requirePermission($mode === 'edit' ? 'ubah kategori' : 'tambah kategori');
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
        $this->categoryId = null;
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3|unique:categories,name,' . $this->categoryId . ',id_category',
        ]);
        $oldCategory = Category::find($this->categoryId);
        $category = Category::updateOrCreate(
            ['id_category' => $this->categoryId],
            ['name' => $this->name]
        );
        session()->flash('message', $this->categoryId ? 'Category updated successfully.' : 'Category created successfully.');

        if ($this->modalMode === 'edit' && $oldCategory) {
            $auditData = [
                'name' => [
                    'before' => $oldCategory->name,
                    'after' => $category->name,
                ],
            ];
            AuditService::log(
                AuditEvent::CATEGORY_UPDATED,
                'category_updated',
                $category,
                ['changes' => $auditData]
            );
        } else {
            AuditService::log(
                AuditEvent::CATEGORY_CREATED,
                'category',
                $category,
                [
                    'name' => $this->name,
                ]
            );
        }

        $this->resetInputFields();
        $this->closeModal();
    }


    // edit category
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;
        $this->updateMode = true;
    }

    public function delete($id)
    {
        $this->requirePermission('hapus kategori');
        $category = Category::find($id);
        $category->delete();
        AuditService::log(
            AuditEvent::CATEGORY_DELETED,
            'category',
            $category,
            [
                'name' => $category->name,
            ]
        );
        session()->flash('message', 'Category deleted successfully.');
        $this->resetPageIfEmpty();
    }

    public function render()
    {
        $categories = $this->getQuery();
        return view('livewire.category.page-category', compact('categories'));
    }
}
