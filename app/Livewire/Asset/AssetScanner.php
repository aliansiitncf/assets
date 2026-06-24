<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AssetScanner extends Component
{
    public $asset = null;

    #[On('assetScanned')]
    public function assetScanned(string $assetCode)
    {
        $assetCode = trim($assetCode);
        // dd($assetCode);
        $this->asset = Asset::with('latestLocation.location')
            ->where('asset_code', $assetCode)->first();

    }
    #[On('reset-scanner')]
    public function resetScanner()
    {
        $this->reset('asset');
    }

    public function render()
    {
        return view('livewire.asset.asset-scanner');
    }
}
