<?php

namespace App\Livewire\Auth;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->intended(route('home'));
        }
    }

    public function register(): \Illuminate\Http\RedirectResponse
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ]);

        $user = app(CreateNewUser::class)->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        Auth::login($user);
        session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
