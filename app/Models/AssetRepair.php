<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRepair extends Model
{
    use HasFactory;

    protected $table = 'asset_repairs';
    protected $guarded = ['id_asset_repair'];
    protected $primaryKey = 'id_asset_repair';

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id_asset');
    }

    public function scopeFilter($query, $startDate = null, $endDate = null)
    {
        return $query->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('completed_at', [$startDate, $endDate]);
        });
    }

    public function components()
    {
        return $this->belongsToMany(Component::class, 'services_components', 'asset_repair_id', 'component_id')
            ->withPivot('merk', 'date', 'store', 'technician', 'qty', 'price')
            ->withTimestamps();
    }
}
