<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetHistories extends Model
{
    use HasFactory;

    protected $table = 'asset_location_histories';
    protected $guarded = ['id_asset_location_history'];
    protected $primaryKey = 'id_asset_location_history';
    protected $keyType = 'int';

    protected $casts = [
        'moved_at' => 'datetime', // sekarang $history->moved_at adalah Carbon instance
    ];
    public function assets()
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id_asset');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id_location');
    }
}
