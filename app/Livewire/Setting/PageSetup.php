<?php

namespace App\Livewire\Setting;

use Livewire\Component;
use App\Models\PageSetup as PageSetupModel;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Page Setup')]
#[Layout('components.layouts.app')]
class PageSetup extends Component
{
    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $size_name, $column, $width, $height, $gap_horizontal, $gap_vertical;

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

    private function resetInputFields()
    {
        $this->size_name = '';
        $this->column = '';
        $this->width = '';
        $this->height = '';
        $this->gap_horizontal = '';
        $this->gap_vertical = '';
    }
    public function edit($id)
    {
        $pageSetup = PageSetupModel::findOrFail($id);
        $this->size_name = $pageSetup->size_name;
        $this->column = $pageSetup->column;
        $this->width = $pageSetup->width;
        $this->height = $pageSetup->height;
        $this->gap_horizontal = $pageSetup->gap_horizontal;
        $this->gap_vertical = $pageSetup->gap_vertical;
    }
    public function save()
    {
        $validatedData = $this->validate([
            'size_name' => 'required|string|max:255',
            'column' => 'required|integer|min:1',
            'width' => 'required|numeric|min:1',
            'height' => 'required|numeric|min:1',
            'gap_horizontal' => 'nullable|numeric|min:0',
            'gap_vertical' => 'nullable|numeric|min:0',
        ]);

        if ($this->modalMode === 'create') {
            PageSetupModel::create($validatedData);
        } elseif ($this->modalMode === 'edit') {
            $pageSetup = PageSetupModel::where('size_name', $this->size_name)->first();
            if ($pageSetup) {
                $pageSetup->update($validatedData);
            }
        }

        $this->closeModal();
        $this->resetInputFields();
    }
    public function render()
    {
        $pages = PageSetupModel::all();
        return view('livewire.setting.page-setup', [
            'pages' => $pages,
        ]);
    }
}
