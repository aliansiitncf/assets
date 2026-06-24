<?php

namespace App\Livewire\Component;

use App\Enums\AuditEvent;
use App\Models\Component as AssComponent;
use App\Services\AuditService;
use App\Traits\HasAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Components')]
#[Layout('components.layouts.app')]
class PageComponent extends Component
{
    use WithPagination, HasAuthorization;
    public $name_component, $componentId;
    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'asc';
    public int $perPage = 10;


    protected $listeners = [
        'component-saved' => '$refresh'
    ];
    private function getQuery()
    {
        return AssComponent::query()
            ->when($this->search, fn($q) => $q->where('name_component', 'like', "%{$this->search}%"))
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
    public function delete($id)
    {
        $this->requirePermission('hapus komponen');
        $component = AssComponent::find($id);
        $component->delete();
        AuditService::log(
            AuditEvent::COMPONENT_DELETED,
            'component_deleted',
            $component,
            [
                'name_component' => $component->name_component,
            ]
        );
        session()->flash('message', 'Component deleted successfully.');
        $this->resetPageIfEmpty();
    }
    public function render()
    {
        $components = $this->getQuery();
        return view('livewire.component.page-component', compact('components'));
    }
}
