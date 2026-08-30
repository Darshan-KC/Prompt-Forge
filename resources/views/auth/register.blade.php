<x-layouts.guest>
    <x-shared.auth-card
        eyebrow="Create your account"
        title="Start building prompts"
        description="Free forever for 25 runs a day. No credit card required."
    >
        @livewire('auth.register')

        <x-slot:footer>
            Already have an account?
            <flux:link href="{{ route('login') }}" wire:navigate class="font-medium">Log in</flux:link>
        </x-slot:footer>
    </x-shared.auth-card>
</x-layouts.guest>