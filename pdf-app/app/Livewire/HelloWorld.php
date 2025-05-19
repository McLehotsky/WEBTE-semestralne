<?php

namespace App\Livewire;

use Livewire\Component;

class HelloWorld extends Component
{
    public $message = 'Ahoj zo Livewire!';

    public function render()
    {
        return view('livewire.hello-world');
    }
}
