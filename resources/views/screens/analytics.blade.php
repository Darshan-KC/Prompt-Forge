<x-layouts.app>
@php
    $analytics = \App\Support\MockData::analytics();
    $period = $analytics['period'];
    $totals = $analytics['totals'];
    $usage = $analytics['usageOverTime'];
    $maxExec = max(1, collect($usage)->max('executions'));
    $maxTokens = max(1, collect($usage)->max('tokens'));
    $maxCost = max(0.01, collect($usage)->max('cost'));

    // Provider donut stops (computed at top level so it's safe inside component slots).
    $donutCumulative = 0;
    $donutStops = [];
    foreach ($analytics['providerUsage'] as $donutProvider) {
        $from = $donutCumulative;
        $donutCumulative += $donutProvider['share'];
        $donutStops[] = $donutProvider['color'].' '.$from.'% '.$donutCumulative.'%';
    }

    // SVG area path for tokens.
    $w = 720; $h = 200; $pad = 8;
    $points = [];
    $n = count($usage);
    foreach ($usage as $idx => $point) {
        $x = $pad + ($idx / max(1, $n - 1)) * ($w - $pad * 2);
        $y = $h - $pad - (($point['tokens'] / $maxTokens) * ($h - $pad * 2));
        $points[] = [$x, $y];
    }
    $line = collect($points)->map(fn ($p) => number_format($p[0], 1).','.number_format($p[1], 1))->implode(' ');
    $area = "{$line} {$w},{$h} {$pad},{$h}";
@endphp

<x-app.page-container class="space-y-6">
    <x-shared.page-header
        eyebrow="Insights"
        title="Analytics"
        :description="$period['label'].' · '.$period['from'].' → '.$period['to']"
    >
        <x-slot:actions>
            <flux:button variant="subtle" icon="arrow-down-tray" x-data x-on:click="$dispatch('pf-toast', { title: 'Export queued', message: 'CSV export is simulated (mock).' })">Export CSV</flux:button>
        </x-slot:actions>
    </x-shared.page-header>

    {{-- Totals --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-shared.metric-card label="Executions" :value="number_format($totals['executions'])" :delta="$totals['executionDelta']" :interval="$period['previousLabel']" />
        <x-shared.metric-card label="Tokens" :value="\App\Support\MockData::compactNumber($totals['tokens'])" :delta="$totals['tokenDelta']" :interval="$period['previousLabel']" />
        <x-shared.metric-card label="Estimated cost" :value="\App\Support\MockData::money($totals['cost'])" :delta="$totals['costDelta']" :interval="$period['previousLabel']" />
        <x-shared.metric-card label="Avg. latency" :value="\App\Support\MockData::latency($totals['avgLatency'])" :delta="$totals['latencyDelta']" :interval="$period['previousLabel']" />
        <x-shared.metric-card label="Success rate" value="97.2%" :delta="$totals['successDelta']" :interval="'of all executions'" />
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        {{-- Executions bar chart --}}
        <section class="rounded-xl border border-zinc-200 bg-white p-4 shadow-xs sm:p-5 dark:border-white/10 dark:bg-zinc-900/60">
            <header class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Executions over time</h2>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $period['label'] }}</p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="size-2 rounded-full bg-brand-500"></span> Runs
                </span>
            </header>
            <div class="mt-5 flex h-48 items-end gap-1.5" role="img" aria-label="Executions bar chart">
                @foreach ($usage as $point)
                    <div class="group relative flex h-full flex-1 flex-col items-center justify-end" title="{{ $point['label'] }} — {{ number_format($point['executions']) }} executions">
                        <div class="w-full rounded-t-[3px] bg-brand-500/70 transition group-hover:bg-brand-500"
                            style="height: {{ max(3, round($point['executions'] / $maxExec * 100)) }}%"></div>
                        <div class="pointer-events-none absolute -top-9 z-10 hidden whitespace-nowrap rounded-md bg-zinc-900 px-2 py-1 text-[0.65rem] font-medium text-white group-hover:block dark:bg-white dark:text-zinc-900">
                            {{ $point['label'] }} · {{ number_format($point['executions']) }} runs
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Tokens area chart --}}
        <section class="rounded-xl border border-zinc-200 bg-white p-4 shadow-xs sm:p-5 dark:border-white/10 dark:bg-zinc-900/60">
            <header class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Token volume</h2>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ number_format($totals['inputTokens']) }} in · {{ number_format($totals['outputTokens']) }} out</p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="h-0.5 w-4 rounded-full bg-brand-500"></span> Tokens
                </span>
            </header>
            <div class="mt-5">
                <svg viewBox="0 0 {{ $w }} {{ $h }}" class="h-48 w-full" preserveAspectRatio="none" role="img" aria-label="Token volume area chart">
                    <defs>
                        <linearGradient id="pf-token-fill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#fb7523" stop-opacity="0.35" />
                            <stop offset="100%" stop-color="#fb7523" stop-opacity="0.02" />
                        </linearGradient>
                    </defs>
                    @foreach ([0.25, 0.5, 0.75] as $g)
                        <line x1="{{ $pad }}" x2="{{ $w - $pad }}" y1="{{ $pad + ($h - $pad * 2) * $g }}" y2="{{ $pad + ($h - $pad * 2) * $g }}" stroke="currentColor" class="text-zinc-100 dark:text-white/5" stroke-width="1" />
                    @endforeach
                    <polygon points="{{ $area }}" fill="url(#pf-token-fill)" />
                    <polyline points="{{ $line }}" fill="none" stroke="#fb7523" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    @if ($n)
                        <circle cx="{{ $points[$n - 1][0] }}" cy="{{ $points[$n - 1][1] }}" r="3.5" fill="#fb7523" />
                    @endif
                </svg>
                <div class="mt-2 flex justify-between font-mono text-[0.65rem] text-zinc-400 dark:text-zinc-500">
                    <span>{{ $usage[0]['label'] }}</span>
                    <span>{{ $usage[$n - 1]['label'] }}</span>
                </div>
            </div>
        </section>
    </div>

    <div class="grid gap-6 xl:grid-cols-5">
        {{-- Model usage --}}
        <section class="rounded-xl border border-zinc-200 bg-white p-4 shadow-xs sm:p-5 xl:col-span-3 dark:border-white/10 dark:bg-zinc-900/60">
            <header class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Usage by model</h2>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">Where your tokens are being spent</p>
                </div>
            </header>
            <ul class="mt-4 space-y-3">
                @foreach ($analytics['modelUsage'] as $model)
                    <li class="group">
                        <div class="flex items-center gap-3">
                            <span class="w-40 truncate text-xs font-medium text-zinc-700 dark:text-zinc-300">{{ $model['model'] }}</span>
                            <div class="relative h-2 flex-1 overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10">
                                <div class="h-full rounded-full bg-brand-500/70 transition group-hover:bg-brand-500" style="width: {{ $model['share'] }}%"></div>
                            </div>
                            <span class="w-12 shrink-0 text-right font-mono text-xs tabular-nums text-zinc-600 dark:text-zinc-300">{{ $model['share'] }}%</span>
                            <div class="hidden w-44 shrink-0 font-mono text-xs text-zinc-400 sm:block dark:text-zinc-500">
                                {{ number_format($model['executions']) }} runs · {{ \App\Support\MockData::compactNumber($model['tokens']) }} tok · {{ \App\Support\MockData::money($model['cost']) }}
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Provider donut --}}
        <section class="rounded-xl border border-zinc-200 bg-white p-4 shadow-xs sm:p-5 xl:col-span-2 dark:border-white/10 dark:bg-zinc-900/60">
            <header>
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">By provider</h2>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">Share of executions</p>
            </header>
            <div class="mt-5 flex items-center gap-6">
                <div class="relative grid size-32 shrink-0 place-items-center rounded-full"
                    style="background: conic-gradient({{ implode(', ', $donutStops) }});">
                    <div class="grid size-24 place-items-center rounded-full bg-white text-center dark:bg-zinc-900">
                        <span class="text-lg font-semibold text-zinc-900 dark:text-white">{{ number_format($totals['executions']) }}</span>
                        <span class="-mt-0.5 text-[0.6rem] text-zinc-400 dark:text-zinc-500">runs</span>
                    </div>
                </div>
                <ul class="flex-1 space-y-2.5">
                    @foreach ($analytics['providerUsage'] as $provider)
                        <li class="flex items-center gap-2">
                            <span class="size-2.5 rounded-full" style="background-color: {{ $provider['color'] }}"></span>
                            <span class="min-w-0 flex-1 truncate text-xs text-zinc-600 dark:text-zinc-300">{{ \App\Support\MockData::provider($provider['provider'])['name'] ?? $provider['provider'] }}</span>
                            <span class="font-mono text-xs tabular-nums text-zinc-500 dark:text-zinc-400">{{ $provider['share'] }}%</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        {{-- Top prompts --}}
        <section class="rounded-xl border border-zinc-200 bg-white p-4 shadow-xs sm:p-5 dark:border-white/10 dark:bg-zinc-900/60">
            <header>
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Top prompts by executions</h2>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $period['label'] }}</p>
            </header>
            <ul class="mt-4 space-y-2.5">
                @foreach ($analytics['topPrompts'] as $idx => $row)
                    <li class="flex items-center gap-3">
                        <span class="w-5 shrink-0 text-right font-mono text-xs tabular-nums text-zinc-400 dark:text-zinc-500">{{ $idx + 1 }}</span>
                        <a href="{{ route('prompts.show', $row['promptId']) }}" wire:navigate class="min-w-0 flex-1 truncate text-sm font-medium text-zinc-800 transition hover:text-brand-600 dark:text-zinc-200 dark:hover:text-brand-400">{{ $row['name'] }}</a>
                        <span class="hidden font-mono text-xs tabular-nums text-zinc-400 sm:block dark:text-zinc-500">{{ \App\Support\MockData::compactNumber($row['tokens']) }} tok</span>
                        <span class="w-16 shrink-0 text-right font-mono text-xs tabular-nums text-zinc-600 dark:text-zinc-300">{{ number_format($row['executions']) }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Expensive prompts --}}
        <section class="rounded-xl border border-zinc-200 bg-white p-4 shadow-xs sm:p-5 dark:border-white/10 dark:bg-zinc-900/60">
            <header>
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Highest avg. cost per run</h2>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">Candidates for model downgrades</p>
            </header>
            <ul class="mt-4 space-y-2.5">
                @foreach ($analytics['expensivePrompts'] as $row)
                    <li class="flex items-center gap-3">
                        <a href="{{ route('prompts.show', $row['promptId']) }}" wire:navigate class="min-w-0 flex-1 truncate text-sm font-medium text-zinc-800 transition hover:text-brand-600 dark:text-zinc-200 dark:hover:text-brand-400">{{ $row['name'] }}</a>
                        <span class="hidden font-mono text-xs tabular-nums text-zinc-400 sm:block dark:text-zinc-500">{{ \App\Support\MockData::latency($row['avgLatency']) }}</span>
                        <span class="w-20 shrink-0 text-right font-mono text-xs font-medium tabular-nums text-brand-600 dark:text-brand-400">{{ \App\Support\MockData::money($row['avgCost']) }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</x-app.page-container>
</x-layouts.app>
