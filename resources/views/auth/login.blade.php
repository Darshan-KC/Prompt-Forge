<x-layouts.guest>
    <x-shared.auth-card
        eyebrow="Welcome back"
        title="Log in to Prompt-Forge"
        description="Run prompts, compare models and manage your library."
    >
        @livewire('auth.login')

        <x-slot:footer>
            Don't have an account?
            <flux:link href="{{ route('register') }}" wire:navigate class="font-medium">Sign up</flux:link>
        </x-slot:footer>
    </x-shared.auth-card>
</x-layouts.guest>