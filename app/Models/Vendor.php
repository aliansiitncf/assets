<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $table = 'vendors';
    protected $guarded = ['id'];

    protected $casts = [
        'is_supplier' => 'boolean',
        'is_service' => 'boolean',
    ];

    public function scopeSuppliers($query)
    {
        return $query->where('is_supplier', true);
    }

    public function scopeServiceProviders($query)
    {
        return $query->where('is_service', true);
    }

    public function getTypeLabelAttribute(): string
    {
        return match (true) {
            $this->is_supplier && $this->is_service => 'Supplier & Jasa Service',
            $this->is_service => 'Jasa Service',
            $this->is_supplier => 'Supplier',
            default => '-',
        };
    }
}
