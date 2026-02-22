<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class NavLink extends Component
{
    public bool $active;

    public function __construct(bool $active = false)
    {
        $this->active = $active;
    }

    public function render(): View
    {
        return view('components.nav-link');
    }
}