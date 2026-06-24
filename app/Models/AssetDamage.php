<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetDamage extends Model
{
    use HasFactory;
    protected $table = 'asset_damages';
    protected $guarded = ['id_asset_damage'];
    protected $primaryKey = 'id_asset_damage';

    protected $casts = [
        'reported_at' => 'datetime'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id_asset');
    }

    public function scopeFilter($query, $startDate = null, $endDate = null)
    {
        return $query->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('reported_at', [$startDate, $endDate]);
        });
    }


}
