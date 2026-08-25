<?php

namespace App\Livewire\Components;

use App\Enums\AuditEvent;
use App\Models\Component as ComponentModel;
use App\Services\AuditService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ComponentModal extends Component
{
    public $componentId = null;
    public $name_component;
    public $isOpen = false;

    public function resetInputFields()
    {
        $this->name_component = '';
        $this->componentId = null;
    }

    #[On('openComponentModal')]
    public function openComponentModal()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    #[On('editComponentModal')]
    public function editComponentModal($id)
    {
        $component = ComponentModel::findOrFail($id);
        $this->componentId = $id;
        $this->name_component = $component->name_component;

        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name_component' => 'required|min:3|unique:components,name_component,' . $this->componentId . ',id_component',
        ]);
        if ($this->componentId) {
            $oldComponent = ComponentModel::find($this->componentId);
            $component = ComponentModel::find($this->componentId);
            $component->update([
                'name_component' => $this->name_component,
            ]);
            $auditData = [
                'name_component' => [
                    'before' => $oldComponent->name_component,
                    'after' => $component->name_component,
                ],
            ];
            AuditService::log(
                AuditEvent::COMPONENT_UPDATED,
                'component_updated',
                $component,
                ['changes' => $auditData]
            );
            $this->isOpen = false;
        } else {
            $component =  ComponentModel::create([
                'name_component' => $this->name_component,
            ]);
            AuditService::log(
                AuditEvent::COMPONENT_CREATED,
                'component_created',
                $component,
                ['name_component' => $this->name_component]
            );
        }
        $this->dispatch('swal', 
            title: 'Success!',
            text: $this->componentId ? 'Component updated successfully.' : 'Component created successfully.',
            icon: 'success'
        );
        $this->dispatch('component-saved');
        $this->resetInputFields();
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.components.component-modal');
    }
}
