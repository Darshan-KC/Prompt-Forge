<div class="space-y-6" x-data="{
    prefs: {
        runs: { email: true, inapp: true },
        versions: { email: true, inapp: true },
        team: { email: false, inapp: true },
        billing: { email: true, inapp: true },
    }
}">
    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
        <header class="border-b border-zinc-100 px-5 py-4 dark:border-white/5">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Notifications</h2>
            <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">Choose what reaches you, and where.</p>
        </header>
        <div class="divide-y divide-zinc-100 dark:divide-white/5">
            @foreach ([
                'runs' => ['Run status', 'When a run completes, errors or gets rate limited', 'bolt'],
                'versions' => ['Version activity', 'When others save or restore a prompt version', 'layers'],
                'team' => ['Team activity', 'Members joining or changing projects', 'users'],
                'billing' => ['Billing & limits', 'Usage thresholds, invoices and payment failures', 'credit-card'],
            ] as $key => [$label, $desc, $icon])
                <div class="flex items-center gap-4 px-5 py-4">
                    <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-zinc-100 text-zinc-500 dark:bg-white/5 dark:text-zinc-400">
                        <flux:icon :icon="$icon" class="size-4" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $label }}</p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $desc }}</p>
                    </div>
                    @foreach (['email' => 'Email', 'inapp' => 'In-app'] as $channel => $channelLabel)
                        <label class="flex cursor-pointer select-none items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                            <input type="checkbox" x-model="prefs.{{ $key }}.{{ $channel }}" class="size-3.5 rounded accent-brand-600" />
                            {{ $channelLabel }}
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="flex items-center justify-end border-t border-zinc-100 px-5 py-4 dark:border-white/5">
            <flux:button variant="primary" icon="check" x-data x-on:click="$dispatch('pf-toast', { icon: 'check-circle', title: 'Preferences saved', message: 'Mock settings only.' })">Save preferences</flux:button>
        </div>
    </section>
</div>