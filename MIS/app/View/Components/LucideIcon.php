<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LucideIcon extends Component
{
    public $name;
    public $class;

    public function __construct($name, $class = '')
    {
        $this->name = $name;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.lucide-icon');
    }
}
