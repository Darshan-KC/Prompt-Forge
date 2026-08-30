<div class="space-y-5">
    <form wire:submit="register" class="space-y-5">
        <flux:field>
            <flux:label>Name</flux:label>
            <flux:input
                wire:model="name"
                type="text"
                name="name"
                autofocus
                autocomplete="name"
                placeholder="Ada Lovelace"
            />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>Email</flux:label>
            <flux:input
                wire:model="email"
                type="email"
                name="email"
                autocomplete="username"
                placeholder="you@example.com"
            />
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>Password</flux:label>
            <div class="relative w-full" x-data="{ show: false }">
                <flux:input
                    wire:model="password"
                    x-bind:type="show ? 'text' : 'password'"
                    name="password"
                    autocomplete="new-password"
                    placeholder="At least 8 characters"
                />
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
            <flux:error name="password" />
        </flux:field>

        <flux:field>
            <flux:label>Confirm password</flux:label>
            <flux:input
                wire:model="password_confirmation"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="Repeat your password"
            />
            <flux:error name="password_confirmation" />
        </flux:field>

        <flux:button variant="primary" type="submit" class="w-full justify-center" wire:loading.attr="disabled" wire:target="register">
            <span wire:loading.remove wire:target="register">Create account</span>
            <span wire:loading wire:target="register" class="inline-flex items-center gap-2">
                <flux:icon.arrow-path class="size-4 animate-spin" />
                Creating account…
            </span>
        </flux:button>

        <p class="text-center text-xs leading-relaxed text-zinc-400 dark:text-zinc-500">
            By creating an account you agree to our Terms of Service and Privacy Policy.
        </p>
    </form>
</div>