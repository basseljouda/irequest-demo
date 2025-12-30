<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DateFilter extends Component
{
    public $placeholder;

    public function __construct($placeholder = 'Select a filter type')
    {
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('components.date-filter');
    }
}
