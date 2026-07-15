<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    use HasFactory;
    protected $table = 'details';
    protected $guarded = ['id'];

    public function assets()
    {
        return $this->belongsToMany(
            Asset::class,
            'assets_details',
            'detail_id',
            'asset_id',
            'id',
            'id_asset'
        )->withPivot('value')
            ->withTimestamps();
    }
}
