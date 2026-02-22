<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Dropdown extends Component
{
    public string $align;
    public string $width;

    public function __construct(string $align = 'right', string $width = '48')
    {
        $this->align = $align;
        $this->width = $width;
    }

    public function render(): View
    {
        return view('components.dropdown');
    }
}