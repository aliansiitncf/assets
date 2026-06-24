<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetComponent extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_asset_component';

    protected $fillable = [
        'asset_id',
        'component_id'
    ];
}
