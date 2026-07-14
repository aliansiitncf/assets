<?php

namespace App\Enums;


class AuditEvent
{
    const ASSET_CREATED = 'asset_created';
    const ASSET_UPDATED = 'asset_updated';
    const ASSET_DELETED = 'asset_deleted';

    const LOGGED_IN = 'logged_in';
    const LOGGED_OUT = 'logged_out';

    const USER_CREATED = 'user_created';
    const USER_UPDATED = 'user_updated';
    const USER_DELETED = 'user_deleted';

    const CATEGORY_CREATED = 'category_created';
    const CATEGORY_UPDATED = 'category_updated';
    const CATEGORY_DELETED = 'category_deleted';

    const LOCATION_CREATED = 'location_created';
    const LOCATION_UPDATED = 'location_updated';
    const LOCATION_DELETED = 'location_deleted';
    const LOCATION_MOVED = 'location_moved';

    const COMPONENT_CREATED = 'component_created';
    const COMPONENT_UPDATED = 'component_updated';
    const COMPONENT_DELETED = 'component_deleted';

    const ASSET_DAMAGED = 'asset_damaged';
    const ASSET_REPAIRED = 'asset_repaired';
    const ASSET_REPAIR_COMPLETED = 'asset_repair_completed';
    const ASSET_REPAIR_UPDATED = 'asset_repair_updated';

    const ASSET_DETAIL_ADDED = 'asset_detail_added';
    const ASSET_DETAIL_UPDATED = 'asset_detail_updated';
}
