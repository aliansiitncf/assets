<?php

namespace App\Livewire\Auth;

use App\Enums\AuditEvent;
use App\Services\AuditService;
use Livewire\Component;

class Logout extends Component
{
    public function logout()
    {
        $user = auth()->user();
        AuditService::log(
            AuditEvent::LOGGED_OUT,
            'auth',
            $user,
            ['email' => $user->email]
        );
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }
    public function render()
    {
        return view('livewire.auth.logout');
    }
}
