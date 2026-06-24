<?php

namespace App\Routes;

use Tabuna\Breadcrumbs\Breadcrumbs;
use Tabuna\Breadcrumbs\Trail;


Breadcrumbs::for('dashboard', function (Trail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});
Breadcrumbs::for('users', function (Trail $trail) {
    $trail->push('Users', route('users'));
});
Breadcrumbs::for('permissions', function (Trail $trail) {
    $trail->push('Permissions', route('permissions'));
});
Breadcrumbs::for('roles', function (Trail $trail) {
    $trail->push('Roles', route('roles'));
});
Breadcrumbs::for('categories', function (Trail $trail) {
    $trail->push('Categories', route('categories'));
});
Breadcrumbs::for('locations', function (Trail $trail) {
    $trail->push('Locations', route('locations'));
});
Breadcrumbs::for('components', function (Trail $trail) {
    $trail->push('Components', route('components'));
});
Breadcrumbs::for('assets', function (Trail $trail) {
    $trail->push('Assets', route('assets'));
});
Breadcrumbs::for('asset.create', function (Trail $trail) {
    $trail->push('Assets', route('assets'));
    $trail->push('Create', route('asset.create'));
});
Breadcrumbs::for('asset.edit', function (Trail $trail, $asset) {
    $trail->push('Assets', route('assets'));
    $trail->push($asset->name, route('asset.edit', $asset));
});
Breadcrumbs::for('asset.damage', function (Trail $trail) {
    $trail->push('Assets', route('assets'));
    $trail->push('Damage', route('asset.damage'));
});
Breadcrumbs::for('asset.repair', function (Trail $trail) {
    $trail->push('Assets', route('assets'));
    $trail->push('Repair', route('asset.repair'));
});
Breadcrumbs::for('asset.report', function (Trail $trail) {
    $trail->push('Assets', route('assets'));
    $trail->push('Report', route('asset.report'));
});

Breadcrumbs::for('page-setup', function (Trail $trail) {
    $trail->push('Page Setup', route('page-setup'));
});
Breadcrumbs::for('asset.print.label', function (Trail $trail) {
    $trail->push('Assets', route('assets'));
    $trail->push('Print Label', route('asset.print.label'));
});

Breadcrumbs::for('audit-log', function (Trail $trail) {
    $trail->push('Audit Log', route('audit-log'));
});
