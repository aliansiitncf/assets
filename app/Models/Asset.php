<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $table = "assets";
    protected $guarded = [
        'id_asset'
    ];
    protected $primaryKey = 'id_asset';
    // protected $fillable = [
    //     'asset_code',
    //     'name',
    //     'purchase_date',
    //     'category_id',
    //     'status',
    // ];
    protected $casts = [
        'purchase_date' => 'date',
        'category_id' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'asset_code';
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id_category');
    }

    public function assets_histories()
    {
        return $this->hasMany(AssetHistories::class, 'asset_id', 'id_asset');
    }

    public function latestLocation()
    {
        return $this->hasOne(AssetHistories::class, 'asset_id', 'id_asset')->latestOfMany('asset_id');
    }

    public function locationHistories()
    {
        return $this->hasMany(AssetHistories::class, 'asset_id', 'id_asset');
    }
    public function components()
    {
        return $this->belongsToMany(
            Component::class,
            'asset_components',
            'asset_id',
            'component_id',
            'id_asset',
            'id_component'
        );
    }
    public function damages()
    {
        return $this->hasMany(AssetDamage::class, 'asset_id', 'id_asset');
    }
    public function repairs()
    {
        return $this->hasMany(AssetRepair::class, 'asset_id', 'id_asset');
    }

    public function activeRepair()
    {
        return $this->hasOne(AssetRepair::class, 'asset_id', 'id_asset')->where('status', 'In Progress');
    }

    protected static function booted()
    {
        static::deleting(function ($asset) {
            if ($asset->image_path && Storage::disk('public')->exists($asset->image_path)) {
                Storage::disk('public')->delete($asset->image_path);
            }
            foreach ($asset->damages as $damage) {
                if ($damage->image_path && Storage::disk('public')->exists($damage->image_path)) {
                    Storage::disk('public')->delete($damage->image_path);
                }
            }
            foreach ($asset->repairs as $repair) {
                if ($repair->image_path && Storage::disk('public')->exists($repair->image_path)) {
                    Storage::disk('public')->delete($repair->image_path);
                }
            }
        });
    }
    public function scopeFilter($query, $category = null, $location = null, $startDate = null, $endDate = null)
    {
        return $query->with(['category', 'components', 'latestLocation.location'])
            ->when($category && $category != 'all', function ($query) use ($category) {
                $query->where('category_id', $category);
            })
            ->when($location && $location != 'all', function ($query) use ($location) {
                $query->whereHas('latestLocation', function ($query) use ($location) {
                    $query->where('location_id', $location);
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('purchase_date', [$startDate, $endDate]);
            });
    }
}
