<?php

namespace App\Livewire\Asset;

use App\Models\Component as ComponentModel;
use Livewire\Component;

class DetailAsset extends Component
{

    public $asset;
    public $search = '';
    public $results;
    public $components = [];
    public $locations = [];

    public function mount($asset)
    {
        $this->locations = $asset->locationHistories()
            ->with('location')
            ->orderBy('moved_at', 'asc')
            ->get()
            ->map(function ($history) {
                return [
                    'name' => $history->location->name ?? 'Unknown',
                    'details' => $history->details,
                    'moved_at' => $history->moved_at ? $history->moved_at->format('d M Y') : '-',
                ];
            })
            ->toArray();

        $this->components = $asset->components
            ->pluck('id_component')
            ->toArray();
    }

    public function close()
    {
        $this->dispatch('closeDetailModal');
    }
    public function render()
    {
        return view('livewire.asset.detail-asset');
    }
}
