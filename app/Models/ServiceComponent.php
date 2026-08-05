<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ServiceComponent extends Pivot
{
    protected $table = 'services_components';

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function technician()
    {
        return $this->belongsTo(Vendor::class, 'technician_id');
    }

    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id');
    }
}
