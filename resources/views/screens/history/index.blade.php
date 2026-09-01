<x-layouts.app>
@php
    $runs = \App\Support\MockData::runs();
    $providers = \App\Support\MockData::providers();
    $statuses = ['success', 'error', 'cancelled'];
@endphp

<x-app.page-container class="space-y-6">
    <x-shared.page-header
        eyebrow="Run history"
        title="Executions"
        description="Every prompt run across every model — with tokens, latency and cost."
    >
        <x-slot:actions>
            <flux:button href="{{ route('playground') }}" wire:navigate variant="primary" icon="bolt">New run</flux:button>
        </x-slot:actions>
    </x-shared.page-header>

    <div x-data="pfRuns({{ Js::from($runs) }})" x-cloak class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <flux:icon.magnifying-glass class="pointer-events-none absolute start-3.5 top-1/2 size-4 -translate-y-1/2 text-zinc-400 dark:text-zinc-500" />
                <input type="search" x-model="q" placeholder="Search runs by prompt, model or output…"
                    class="block w-full rounded-lg border-0 bg-white py-2.5 ps-10 pe-10 text-sm text-zinc-800 shadow-xs ring-1 ring-inset ring-zinc-200 transition placeholder:text-zinc-400 focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-900/60 dark:text-zinc-200 dark:ring-white/10">
                <button type="button" x-show="q" x-on:click="q = ''" class="absolute end-2 top-1/2 -translate-y-1/2 rounded p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200" aria-label="Clear search">
                    <flux:icon.x-mark class="size-4" />
                </button>
            </div>

            <select x-model="status"
                class="block cursor-pointer rounded-lg border-0 bg-white px-3 py-2.5 text-sm text-zinc-700 shadow-xs ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-900/60 dark:text-zinc-300 dark:ring-white/10">
                <option value="all">All statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}">{{ \App\Support\MockData::statusConfig($status)['label'] }}</option>
                @endforeach
            </select>

            <select x-model="provider"
                class="block cursor-pointer rounded-lg border-0 bg-white px-3 py-2.5 text-sm text-zinc-700 shadow-xs ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-900/60 dark:text-zinc-300 dark:ring-white/10">
                <option value="all">All providers</option>
                @foreach ($providers as $provider)
                    <option value="{{ $provider['slug'] }}">{{ $provider['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
            <div class="flex items-center justify-between px-4 py-3 sm:px-5">
                <p class="text-xs text-zinc-400 dark:text-zinc-500" x-show="visibleCount() > 0">
                    Showing <span x-text="visibleCount()"></span> of {{ count($runs) }} runs
                </p>
            </div>

            {{-- Header row --}}
            <div class="hidden items-center gap-3 border-y border-zinc-100 px-4 py-2 text-[0.7rem] font-semibold uppercase tracking-wider text-zinc-400 sm:flex lg:px-5 dark:border-white/5 dark:text-zinc-500">
                <span class="flex-1">Prompt</span>
                <span class="hidden w-40 shrink-0 md:block">Model</span>
                <span class="w-24 shrink-0">Status</span>
                <span class="w-20 shrink-0 text-right">Tokens</span>
                <span class="hidden w-20 shrink-0 text-right md:block">Latency</span>
                <span class="hidden w-20 shrink-0 text-right lg:block">Cost</span>
                <span class="w-24 shrink-0 text-right">When</span>
            </div>

            <div>
                @foreach ($runs as $i => $run)
                    <a href="{{ route('history.show', $run['id']) }}" wire:navigate x-show="filtered({{ $i }})" x-cloak
                        class="flex flex-wrap items-center gap-x-3 gap-y-2 border-b border-zinc-100 px-4 py-3 transition last:border-0 hover:bg-zinc-800/[0.02] sm:flex-nowrap lg:px-5 dark:border-white/5 dark:hover:bg-white/[0.02]">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $run['promptName'] }}</p>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500">#{{ $run['id'] }} · {{ $run['category'] }}</p>
                        </div>

                        <x-shared.model-badge :slug="$run['model']" :provider-slug="$run['provider']" class="hidden w-40 shrink-0 md:inline-flex" />

                        <x-shared.status-badge :status="$run['status']" :dot="false" class="w-24 shrink-0" />

                        <span class="w-20 shrink-0 text-right font-mono text-xs tabular-nums text-zinc-600 dark:text-zinc-300">{{ number_format($run['tokens']) }}</span>
                        <span class="hidden w-20 shrink-0 text-right font-mono text-xs tabular-nums text-zinc-500 md:block dark:text-zinc-400">
                            {{ $run['status'] === 'success' ? \App\Support\MockData::latency($run['latencyMs']) : '—' }}
                        </span>
                        <span class="hidden w-20 shrink-0 text-right font-mono text-xs tabular-nums text-zinc-500 lg:block dark:text-zinc-400">
                            {{ $run['cost'] > 0 ? \App\Support\MockData::money($run['cost']) : '—' }}
                        </span>
                        <span class="w-24 shrink-0 text-right text-xs text-zinc-400 dark:text-zinc-500">{{ \App\Support\MockData::timeAgo($run['createdAt']) }}</span>
                    </a>
                @endforeach
            </div>

            <div x-show="visibleCount() === 0" x-cloak>
                <x-shared.empty-state icon="clock" title="No runs match" description="Adjust the search or filters to find what you're looking for.">
                    <flux:button variant="subtle" size="sm" x-on:click="q = ''; status = 'all'; provider = 'all'">Clear filters</flux:button>
                </x-shared.empty-state>
            </div>
        </div>
    </div>
</x-app.page-container>
</x-layouts.app>
