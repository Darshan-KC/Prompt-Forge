<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->intended(route('home'));
        }
    }

    public function login(): \Illuminate\Http\RedirectResponse
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();

            return redirect()->intended(route('home'));
        }

        session()->flash('status', 'auth.failed');

        return back()->withErrors([
            'email' => __('auth.failed'),
        ]);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
