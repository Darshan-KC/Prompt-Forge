<x-layouts.app>
@php
    $dashboard = \App\Support\MockData::dashboard();
    $stats = $dashboard['stats'];
    $activity = \App\Support\MockData::activity();
    $recentPrompts = \App\Support\MockData::recentPrompts(4);
    $recentRuns = \App\Support\MockData::recentRuns(5);
    $user = $dashboard['user'];
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

    $activityIconMap = [
        'run' => ['icon' => 'play', 'classes' => 'bg-brand-500/10 text-brand-600 dark:text-brand-400'],
        'version' => ['icon' => 'rectangle-stack', 'classes' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400'],
        'favorite' => ['icon' => 'star', 'classes' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400'],
        'project' => ['icon' => 'folder', 'classes' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'],
    ];
@endphp

<x-app.page-container class="space-y-6">

    <x-shared.page-header
        eyebrow="Workspace overview"
        :title="$greeting.', '.$user['firstName']"
        description="Here's what's happening across your prompts, models and spend."
    >
        <x-slot:actions>
            <flux:button href="{{ route('prompts.create') }}" wire:navigate variant="primary" icon="plus">New prompt</flux:button>
            <flux:button href="{{ route('playground') }}" wire:navigate icon="play">Playground</flux:button>
        </x-slot:actions>
    </x-shared.page-header>

    {{-- Quick stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <x-shared.metric-card
                :label="$stat['label']"
                :value="match ($stat['format']) {
                    'compact' => \App\Support\MockData::compactNumber($stat['value']),
                    'currency' => \App\Support\MockData::money($stat['value']),
                    'latency' => \App\Support\MockData::latency($stat['value']),
                    default => number_format($stat['value']),
                }"
                :delta="$stat['delta']"
                :interval="$stat['interval']"
            />
        @endforeach
    </div>

    {{-- Quick actions --}}
    <div class="grid gap-4 lg:grid-cols-3">
        <a href="{{ route('prompts.create') }}" wire:navigate class="group flex items-center gap-4 rounded-xl border border-zinc-200 bg-white p-4 shadow-xs transition hover:border-brand-500/40 hover:shadow-md dark:border-white/10 dark:bg-zinc-900/60 dark:hover:border-brand-400/30">
            <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-brand-500/10 text-brand-600 transition group-hover:bg-brand-500/15 dark:text-brand-400">
                <flux:icon.pencil-square class="size-5" />
            </span>
            <span class="min-w-0">
                <span class="block text-sm font-semibold text-zinc-900 dark:text-white">Craft a new prompt</span>
                <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">Start from a blank editor or a template</span>
            </span>
            <flux:icon.chevron-right class="ml-auto size-4 shrink-0 text-zinc-300 transition group-hover:translate-x-0.5 group-hover:text-brand-500 dark:text-zinc-600" />
        </a>

        <a href="{{ route('playground') }}" wire:navigate class="group flex items-center gap-4 rounded-xl border border-zinc-200 bg-white p-4 shadow-xs transition hover:border-brand-500/40 hover:shadow-md dark:border-white/10 dark:bg-zinc-900/60 dark:hover:border-brand-400/30">
            <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-sky-500/10 text-sky-600 transition group-hover:bg-sky-500/15 dark:text-sky-400">
                <flux:icon.play class="size-5" />
            </span>
            <span class="min-w-0">
                <span class="block text-sm font-semibold text-zinc-900 dark:text-white">Run in the playground</span>
                <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">Execute any prompt against any model instantly</span>
            </span>
            <flux:icon.chevron-right class="ml-auto size-4 shrink-0 text-zinc-300 transition group-hover:translate-x-0.5 group-hover:text-brand-500 dark:text-zinc-600" />
        </a>

        <a href="{{ route('settings.providers') }}" wire:navigate class="group flex items-center gap-4 rounded-xl border border-zinc-200 bg-white p-4 shadow-xs transition hover:border-brand-500/40 hover:shadow-md dark:border-white/10 dark:bg-zinc-900/60 dark:hover:border-brand-400/30">
            <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-emerald-500/10 text-emerald-600 transition group-hover:bg-emerald-500/15 dark:text-emerald-400">
                <flux:icon.server-stack class="size-5" />
            </span>
            <span class="min-w-0">
                <span class="block text-sm font-semibold text-zinc-900 dark:text-white">Configure providers</span>
                <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">Connect API keys for new model providers</span>
            </span>
            <flux:icon.chevron-right class="ml-auto size-4 shrink-0 text-zinc-300 transition group-hover:translate-x-0.5 group-hover:text-brand-500 dark:text-zinc-600" />
        </a>
    </div>

    {{-- Recent prompts + runs --}}
    <div class="grid gap-6 xl:grid-cols-2">
        <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
            <header class="flex items-center justify-between px-4 py-3 lg:px-5">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Recent prompts</h2>
                <flux:link href="{{ route('prompts.index') }}" wire:navigate variant="subtle" size="sm" icon:trailing="arrow-right">View all</flux:link>
            </header>
            <div class="border-t border-zinc-100 dark:border-white/5">
                @php($promptIds = $recentPrompts->pluck('id'))
                @foreach ($recentPrompts as $prompt)
                    <x-prompt.prompt-row :prompt="$prompt" />
                @endforeach
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
            <header class="flex items-center justify-between px-4 py-3 lg:px-5">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Latest runs</h2>
                <flux:link href="{{ route('history.index') }}" wire:navigate variant="subtle" size="sm" icon:trailing="arrow-right">View history</flux:link>
            </header>
            <div class="border-t border-zinc-100 dark:border-white/5">
                @foreach ($recentRuns as $run)
                    <a href="{{ route('history.show', $run['id']) }}" wire:navigate class="group flex items-center gap-3 border-b border-zinc-100 px-4 py-3 transition last:border-0 hover:bg-zinc-800/[0.02] lg:px-5 dark:border-white/5 dark:hover:bg-white/[0.02]">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $run['promptName'] }}</span>
                                <x-shared.status-badge :status="$run['status']" :dot="false" />
                            </div>
                            <div class="mt-0.5 flex items-center gap-2 text-xs text-zinc-400 dark:text-zinc-500">
                                <span>{{ \App\Support\MockData::timeAgo($run['createdAt']) }}</span>
                                <span aria-hidden="true">·</span>
                                <span class="tabular-nums">{{ number_format($run['tokens']) }} tokens</span>
                                @if ($run['status'] === 'success')
                                    <span aria-hidden="true">·</span>
                                    <span class="tabular-nums">{{ \App\Support\MockData::latency($run['latencyMs']) }}</span>
                                @endif
                            </div>
                        </div>
                        <x-shared.model-badge :slug="$run['model']" :provider-slug="$run['provider']" class="hidden w-40 shrink-0 sm:inline-flex" />
                    </a>
                @endforeach
            </div>
        </section>
    </div>

    {{-- Activity feed --}}
    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
        <header class="px-4 py-3 lg:px-5">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Activity</h2>
            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Everything your team ran and changed recently.</p>
        </header>
        <ol class="border-t border-zinc-100 px-5 py-4 lg:px-6 dark:border-white/5">
            @foreach ($activity as $i => $entry)
                @php($meta = $activityIconMap[$entry['type']] ?? $activityIconMap['run'])
                <li class="relative flex gap-3.5 pb-6 last:pb-0">
                    @if (! $loop->last)
                        <span class="absolute left-[11px] top-8 h-[calc(100%-1.75rem)] w-px bg-zinc-200 dark:bg-white/10" aria-hidden="true"></span>
                    @endif
                    <span class="relative z-10 grid size-6 shrink-0 place-items-center rounded-full {{ $meta['classes'] }}">
                        <flux:icon :icon="$meta['icon']" class="size-3" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm leading-snug text-zinc-700 dark:text-zinc-300">
                            @if ($entry['promptId'])
                                <flux:link href="{{ route('prompts.show', $entry['promptId']) }}" wire:navigate class="font-medium text-zinc-900 dark:text-white">{{ $entry['text'] }}</flux:link>
                            @else
                                {{ $entry['text'] }}
                            @endif
                        </p>
                        <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">
                            <time>{{ \App\Support\MockData::timeAgo($entry['at']) }}</time>
                            @if ($entry['meta'])
                                <span aria-hidden="true"> · </span>{{ $entry['meta'] }}
                            @endif
                        </p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>
</x-app.page-container>
</x-layouts.app>
