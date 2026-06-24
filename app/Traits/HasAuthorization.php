<?php

namespace App\Traits;

trait HasAuthorization
{
  
    protected function requireRole($role)
    {
        if (!auth()->user()->hasRole($role,)) {
            $message ="Hanya pengguna dengan peran {$role} yang dapat mengakses halaman ini.";
            abort(403, $message);
        }
    }

    protected function requirePermission($permission)
    {
        if (!auth()->user()->can($permission)) {
            $message = "Hanya pengguna dengan izin {$permission} yang dapat mengakses halaman ini.";
            abort(403, $message);
        }
    }

    protected function canAccess($permission)
    {
        return auth()->user()->can($permission);
    }

}
