<div class="space-y-6">
    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
        <header class="border-b border-zinc-100 px-5 py-4 dark:border-white/5">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Password</h2>
            <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">Use a strong password you don't reuse elsewhere.</p>
        </header>
        <div class="space-y-4 p-5 sm:p-6">
            <flux:field>
                <flux:label>Current password</flux:label>
                <flux:input type="password" autocomplete="current-password" />
            </flux:field>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>New password</flux:label>
                    <flux:input type="password" autocomplete="new-password" />
                </flux:field>
                <flux:field>
                    <flux:label>Confirm new password</flux:label>
                    <flux:input type="password" autocomplete="new-password" />
                </flux:field>
            </div>
            <div class="flex justify-end border-t border-zinc-100 pt-5 dark:border-white/5">
                <flux:button variant="primary" icon="check" x-data x-on:click="$dispatch('pf-toast', { icon: 'check-circle', title: 'Password updated', message: 'Wired to Fortify once the backend acts.' })">Update password</flux:button>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
        <header class="border-b border-zinc-100 px-5 py-4 dark:border-white/5">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Two-factor authentication</h2>
            <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">Add an extra layer of security to your account.</p>
        </header>
        <div class="flex items-center justify-between gap-4 p-5 sm:p-6" x-data="{ enabled: true }">
            <div>
                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200" x-text="enabled ? 'Enabled' : 'Disabled'"></p>
                <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500" x-text="enabled ? 'Authenticator app or recovery codes.' : 'Not protecting this account.'"></p>
            </div>
            <button type="button" role="switch" x-on:click="enabled = !enabled" :aria-checked="enabled"
                class="relative inline-flex h-5.5 w-10 shrink-0 items-center rounded-full transition"
                :class="enabled ? 'bg-brand-600' : 'bg-zinc-300 dark:bg-white/15'">
                <span class="inline-block size-4 translate-x-0.5 rounded-full bg-white shadow transition" :class="enabled ? 'translate-x-[22px]' : 'translate-x-0.5'"></span>
            </button>
        </div>
        <div class="flex items-center justify-between gap-4 border-t border-zinc-100 px-5 py-4 sm:px-6 dark:border-white/5">
            <span class="text-xs text-zinc-400 dark:text-zinc-500">Sessions</span>
            <flux:button variant="subtle" size="sm" icon="shield-exclamation" x-data x-on:click="$dispatch('pf-toast', { title: 'Sessions cleared', message: 'Mock operation.' })">Sign out of other devices</flux:button>
        </div>
    </section>

    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
        <div class="flex flex-col justify-between gap-4 p-5 sm:flex-row sm:items-center sm:p-6">
            <div>
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Danger zone</h2>
                <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">Permanently delete your workspace and all prompts.</p>
            </div>
            <flux:button variant="danger" size="sm" x-data="{ confirm: false }" x-on:click="confirm = true" x-bind:disabled="confirm">
                <span x-text="confirm ? 'Deletion is mocked' : 'Delete workspace'"></span>
            </flux:button>
        </div>
    </section>
</div>