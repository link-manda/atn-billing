<?php

namespace App\Livewire\Layout;

use Livewire\Component;

class Topbar extends Component
{
    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    }

    public function render()
    {
        return view('livewire.layout.topbar');
    }
}
