<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Tabuna\Breadcrumbs\Breadcrumbs as BreadcrumbsFacade;

class Breadcrumbs extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumbs');
    }
        public function shouldRender(): bool
    {
        return BreadcrumbsFacade::has();
    }
}
