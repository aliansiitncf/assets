<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_component',
    ];
    protected $primaryKey = 'id_component';
    public function asset()
    {
        return $this->belongsToMany(Asset::class, 'asset_components', 'component_id', 'asset_id', 'id_component', 'id_asset');
    }
}
