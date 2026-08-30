<x-layouts.guest>
    <x-shared.auth-card
        eyebrow="Security"
        title="Confirm your password"
        description="This action is protected. Confirm your password to continue."
    >
        <form method="POST" action="{{ route('password.confirm.store') }}" class="space-y-5">
            @csrf

            <flux:field>
                <flux:label>Password</flux:label>
                <div class="relative w-full" x-data="{ show: false }">
                    <flux:input x-bind:type="show ? 'text' : 'password'" name="password" required autofocus autocomplete="current-password" placeholder="••••••••" />
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

            <flux:button variant="primary" type="submit" class="w-full justify-center">Confirm</flux:button>
        </form>
    </x-shared.auth-card>
</x-layouts.guest>