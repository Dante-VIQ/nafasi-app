<?php

namespace App\Livewire;

use Livewire\Component;

class AboutPage extends Component
{
    /**
     * Livewire layout to use for this component.
     *
     * This avoids calling the view()->layout() helper which may be
     * reported as an undefined method in some environments.
     *
     * @var string
     */
    protected $layout = 'layouts.guest';
    public function render()
    {
        return view('livewire.about-page');
    }
}