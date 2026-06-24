<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;

class AuditService
{
    public static function log (
        string $event,
        string $logName,
        $subject = null,
        array $properties = []
    ):Activity {
        return activity($logName)
            ->event($event)
            ->causedBy(auth()->user())
            ->performedOn($subject)
            ->withProperties($properties)
            ->log($event);
    }
}
