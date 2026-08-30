<x-layouts.guest>
    <x-shared.auth-card
        eyebrow="Recover access"
        title="Reset your password"
        description="Enter your account email and we'll send a secure reset link."
    >
        @if (session('status'))
            <div class="flex items-start gap-2.5 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-3.5 py-2.5 text-sm text-emerald-700 dark:text-emerald-300">
                <flux:icon.check-circle class="mt-0.5 size-4 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com" />
                @error('email')
                    <div class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</div>
                @enderror
            </flux:field>

            <flux:button variant="primary" type="submit" class="w-full justify-center">Send reset link</flux:button>
        </form>

        <x-slot:footer>
            Remembered it?
            <flux:link href="{{ route('login') }}" wire:navigate class="font-medium">Back to log in</flux:link>
        </x-slot:footer>
    </x-shared.auth-card>
</x-layouts.guest>