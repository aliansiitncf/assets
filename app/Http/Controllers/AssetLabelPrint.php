<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\PageSetup;
use Illuminate\Http\Request;

class AssetLabelPrint extends Controller
{
    public function print(Request $request)
    {
        if (!$request->has('assets')) {
            abort(404, 'No assets selected');
        }

        $assetCodes = is_array($request->assets)
            ? $request->assets
            : explode(',', $request->assets);

        $assets = Asset::whereIn('asset_code', $assetCodes)->get();

        if ($assets->isEmpty()) {
            abort(404, 'Assets empty');
        }

        $pageSetup = PageSetup::findOrFail($request->page_setup);

        return view('exports.qr-export', compact('assets', 'pageSetup'));
    }
}
