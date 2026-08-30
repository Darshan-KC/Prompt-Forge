<x-layouts.guest>
    <x-shared.auth-card
        eyebrow="Recover access"
        title="Choose a new password"
        description="Pick a strong password for your account."
    >
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}" />

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@example.com" />
                @error('email')
                    <div class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</div>
                @enderror
            </flux:field>

            <flux:field>
                <flux:label>New password</flux:label>
                <div class="relative w-full" x-data="{ show: false }">
                    <flux:input x-bind:type="show ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="At least 8 characters" />
                    <button
                        type="button"
                        class="absolute end-2.5 top-1/2 -translate-y-1/2 rounded p-1 text-zinc-400 transition hover:text-zinc-600 dark:hover:text-zinc-200"
                        x-on:click="show = !show"
                        x-bind:aria-label="show ? 'Hide password' : 'Show password'"
                        tabindex="-1"
                    >
                        <flux:icon x-cloak x-show="!show" icon="eye" class="size-4" />
                        <flux:icon x-cloak x-show="show" icon="eye-slash" class="size-4" />
                    </button>
                </div>
                @error('password')
                    <div class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</div>
                @enderror
            </flux:field>

            <flux:field>
                <flux:label>Confirm new password</flux:label>
                <flux:input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" />
            </flux:field>

            <flux:button variant="primary" type="submit" class="w-full justify-center">Reset password</flux:button>
        </form>

        <x-slot:footer>
            Back to
            <flux:link href="{{ route('login') }}" wire:navigate class="font-medium">log in</flux:link>
        </x-slot:footer>
    </x-shared.auth-card>
</x-layouts.guest>