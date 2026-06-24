<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        /* =====================
         |  COUNTERS (4 query)
         ===================== */
        $assetCount    = Asset::count();
        $categoryCount = Category::count();
        $locationCount = Location::count();
        $userCount     = User::count();

        /* ===========================
         |  CHART ASSET PER KATEGORI
         |  (1 query)
         =========================== */
        $chartCategory = Category::withCount('assets')
            ->get()
            ->map(fn($c) => [
                'name'  => $c->name,
                'value' => $c->assets_count,
            ]);

        /* ===========================
         |  CHART ASSET PER LOKASI
         |  (1 query, optimized)
         =========================== */
        $chartLocation = DB::table('asset_location_histories as alh')
            ->join(
                DB::raw('(SELECT asset_id, MAX(id_asset_location_history) AS max_id
                          FROM asset_location_histories
                          GROUP BY asset_id) latest'),
                'alh.id_asset_location_history',
                '=',
                'latest.max_id'
            )
            ->join('locations', 'locations.id_location', '=', 'alh.location_id')
            ->select(
                'locations.name as name',
                DB::raw('COUNT(*) as value')
            )
            ->groupBy('locations.name')
            ->get();

        return view('livewire.dashboard', compact(
            'assetCount',
            'categoryCount',
            'locationCount',
            'userCount',
            'chartCategory',
            'chartLocation'
        ));
    }
}
