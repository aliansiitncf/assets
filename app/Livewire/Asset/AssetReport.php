<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\PageSetup;
use App\Traits\HasAuthorization;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;


#[Title('Asset Report')]
#[Layout('components.layouts.app')]
class AssetReport extends Component
{
    use WithPagination, HasAuthorization;
    public $perPage = 10;
    public $search;
    public $pageSetup;
    public $showModal = false;

    // selection
    public $selected = [], $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = Asset::where('asset_code', 'like', '%' . $this->search . '%')
                ->paginate($this->perPage)
                ->pluck('asset_code')
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $this->selectAll = false;
    }

    /** reset saat search */
    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectAll = false;
        $this->selected = [];
    }

    public function print()
    {
        $this->validate([
            'pageSetup' => 'required|exists:page_setups,id_page_setup',
            'selected'  => 'required|array|min:1',
        ]);

        $query = http_build_query([
            'page_setup' => $this->pageSetup,
            'assets'     => $this->selected,
        ]);

        $this->dispatch(
            'open-print-tab',
            url: route('asset.print.label') . '?' . $query
        );

        $this->closeModal();
        // $this->resetPage();
    }


    public function closeModal()
    {
        $this->showModal = false;
        $this->reset('pageSetup');
    }


    public function render()
    {
        $assets = Asset::with(['category', 'components', 'latestLocation.location'])
            ->where('asset_code', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);
        $pages = PageSetup::all();
        return view('livewire.asset.asset-report', [
            'assets' => $assets,
            'pages' => $pages,
        ]);
    }
}
