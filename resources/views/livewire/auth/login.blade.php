<div class="space-y-5">
    @if (session('status'))
        <div class="flex items-start gap-2.5 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-3.5 py-2.5 text-sm text-emerald-700 dark:text-emerald-300">
            <flux:icon.check-circle class="mt-0.5 size-4 shrink-0" />
            <span>{{ __($statusKey ?? 'auth.failed') }}</span>
        </div>
    @endif

    <form wire:submit="login" class="space-y-5">
        <flux:field>
            <flux:label>Email</flux:label>
            <flux:input
                wire:model="email"
                type="email"
                name="email"
                autofocus
                autocomplete="username"
                placeholder="you@example.com"
            />
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>Password</flux:label>
            <div class="relative w-full" x-data="{ show: false }">
                <flux:input
                    x-ref="password"
                    wire:model="password"
                    x-bind:type="show ? 'text' : 'password'"
                    name="password"
                    autocomplete="current-password"
                    placeholder="••••••••"
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

        <div class="flex items-center justify-between gap-4">
            <flux:checkbox wire:model="remember" label="Remember me" />
            @if (Route::has('password.request'))
                <flux:link href="{{ route('password.request') }}" variant="subtle" class="text-sm">Forgot password?</flux:link>
            @endif
        </div>

        <flux:button variant="primary" type="submit" class="w-full justify-center" wire:loading.attr="disabled" wire:target="login">
            <span wire:loading.remove wire:target="login">Log in</span>
            <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                <flux:icon.arrow-path class="size-4 animate-spin" />
                Logging in…
            </span>
        </flux:button>
    </form>

    <div class="relative">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-zinc-200 dark:border-white/10"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="bg-white px-3 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:bg-zinc-900 dark:text-zinc-500">Secure sign in</span>
        </div>
    </div>
</div>