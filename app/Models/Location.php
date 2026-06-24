<?php

namespace App\Models;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    protected $fillable = ['name'];
    protected $primaryKey = 'id_location';
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
    public function latestLocationAssets()
{
    return $this->hasMany(AssetHistories::class, 'location_id');
}
}
