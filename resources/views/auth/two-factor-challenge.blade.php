<x-layouts.guest>
    <x-shared.auth-card
        eyebrow="Two-factor authentication"
        title="Verify it's you"
        description="Enter the six-digit code from your authenticator app, or use a recovery code."
    >
        <form method="POST" action="{{ route('two-factor.login.store') }}" class="space-y-5" x-data="{ mode: 'code' }">
            @csrf

            <flux:field x-show="mode === 'code'">
                <flux:label>Authentication code</flux:label>
                <flux:input type="text" name="code" inputmode="numeric" autofocus autocomplete="one-time-code" placeholder="000 000" />
            </flux:field>

            <flux:field x-cloak x-show="mode === 'recovery'">
                <flux:label>Recovery code</flux:label>
                <flux:input type="text" name="recovery_code" autocomplete="one-time-code" placeholder="recovery-code-XXXX-XXXX" />
            </flux:field>

            @error('code')
                <div class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror

            <flux:button variant="primary" type="submit" class="w-full justify-center">Continue</flux:button>

            <div class="flex items-center justify-between gap-4 text-sm">
                <button
                    type="button"
                    class="text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300"
                    x-on:click="mode = mode === 'code' ? 'recovery' : 'code'"
                >
                    <span x-text="mode === 'code' ? 'Use a recovery code' : 'Use an authenticator code'"></span>
                </button>
                <flux:link href="{{ route('login') }}" wire:navigate variant="subtle">Back to log in</flux:link>
            </div>
        </form>
    </x-shared.auth-card>
</x-layouts.guest>