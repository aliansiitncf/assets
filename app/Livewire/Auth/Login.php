<?php

namespace App\Livewire\Auth;

use App\Enums\AuditEvent;
use App\Services\AuditService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Login')]
#[Layout('components.layouts.guest')]
class Login extends Component
{
    public $name = '';
    public $password = '';
    public $remember = false;


    public $messages = [
        'name.required' => 'name field is required.',
        'password.required' => 'The password field is required.',
        'password.min' => 'The password must be at least 6 characters.',
    ];
    protected $rules = [
        'name' => 'required',
        'password' => 'required|min:6',
    ];
    public function login()
    {
        $this->validate();

        if (auth()->attempt(['name' => $this->name, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            AuditService::log(
                AuditEvent::LOGGED_IN,
                'auth',
                auth()->user(),
                ['name' => auth()->user()->name]
            );
            return redirect()->intended('/dashboard');
        }
        session()->flash('error', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
