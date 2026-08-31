@php
    $promptId = is_numeric($prompt ?? null) ? (int) $prompt : null;
    $promptData = $promptId !== null && $promptId > 0 ? \App\Support\MockData::prompt($promptId) : null;
    $folder = $promptData && $promptData['folder'] ? \App\Support\MockData::folder($promptData['folder']) : null;
    $project = $promptData && $promptData['projectId'] ? \App\Support\MockData::project($promptData['projectId']) : null;
    $versions = $promptData ? \App\Support\MockData::versionsFor($promptData['id']) : [];
    $latestRun = $promptData ? collect(\App\Support\MockData::runs())->firstWhere('promptId', $promptData['id']) : null;
@endphp

<x-app.page-container class="space-y-6">
    @if (! $promptData)
        <div class="mx-auto max-w-sm py-20">
            <x-shared.empty-state icon="document-text" title="Prompt not found" description="This prompt doesn't exist in the mock library.">
                <flux:button href="{{ route('prompts.index') }}" wire:navigate variant="primary" size="sm">Back to library</flux:button>
            </x-shared.empty-state>
        </div>
    @else
        <div x-data="{ fav: {{ Js::from($promptData['favorite']) }}, vtab: 'system' }" x-cloak>
            <x-shared.page-header
                :eyebrow="trim($promptData['category'].(($folder ?? null) ? ' · '.$folder['name'] : ''))"
                :title="$promptData['name']"
                :description="$promptData['description']"
                :kbd="'v'.$promptData['version']"
            >
                <x-slot:actions>
                    <flux:button variant="primary" icon="play" href="{{ route('playground', ['prompt' => $promptData['id']]) }}" wire:navigate>Run in playground</flux:button>
                    <flux:button variant="subtle" icon="arrows-right-left" href="{{ route('prompts.compare', $promptData['id']) }}" wire:navigate class="hidden sm:inline-flex">Compare</flux:button>
                    <x-app.prompt-menu :prompt="$promptData" />
                </x-slot:actions>
            </x-shared.page-header>

            <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                <div class="flex items-center gap-2.5">
                    <x-shared.status-badge :status="$promptData['status']" :dot="false" />
                    <span class="inline-flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                        <flux:icon.clock class="size-3.5" />
                        Updated {{ \App\Support\MockData::timeAgo($promptData['updatedAt']) }}
                    </span>
                </div>
                <x-shared.model-badge :slug="$promptData['model']" :provider-slug="$promptData['provider']" />
                <span class="inline-flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                    <flux:icon.bolt class="size-3.5" />
                    {{ number_format($promptData['usageCount']) }} runs
                </span>
                @if ($project)
                    <flux:link href="{{ route('projects.show', $project['id']) }}" wire:navigate variant="subtle" size="sm" class="inline-flex items-center gap-1.5">
                        <flux:icon.folder class="size-3.5" />
                        {{ $project['name'] }}
                    </flux:link>
                @endif
                <button type="button" x-on:click="fav = !fav"
                    class="inline-flex items-center gap-1.5 rounded-md px-1.5 py-1 text-xs font-medium transition"
                    :class="fav ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'">
                    <flux:icon.star class="size-3.5" x-bind:variant="fav ? 'solid' : 'outline'" x-bind:class="fav ? 'text-amber-500' : ''" />
                    <span x-text="fav ? 'Starred' : 'Star'"></span>
                </button>
            </div>

            <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
                <div class="min-w-0 space-y-6">
                    {{-- Editor --}}
                    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                        <header class="flex items-center justify-between border-b border-zinc-100 px-4 py-2.5 sm:px-5 dark:border-white/5">
                            <div class="flex items-center gap-1 rounded-lg bg-zinc-100 p-1 dark:bg-white/5">
                                <button type="button" x-on:click="vtab = 'system'"
                                    class="rounded-md px-3 py-1 text-xs font-medium transition"
                                    :class="vtab === 'system' ? 'bg-white text-zinc-900 shadow-xs dark:bg-zinc-800 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'">System</button>
                                <button type="button" x-on:click="vtab = 'message'"
                                    class="rounded-md px-3 py-1 text-xs font-medium transition"
                                    :class="vtab === 'message' ? 'bg-white text-zinc-900 shadow-xs dark:bg-zinc-800 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'">Message</button>
                            </div>
                            <flux:link href="{{ route('prompts.versions', $promptData['id']) }}" wire:navigate variant="subtle" size="sm" icon="book-open" class="hidden sm:inline-flex">v{{ $promptData['version'] }} history</flux:link>
                        </header>
                        <div class="relative min-h-[280px]">
                            <pre x-show="vtab === 'system'" x-cloak class="whitespace-pre-wrap border-b border-zinc-100 p-5 font-mono text-sm leading-relaxed text-zinc-700 dark:border-white/5 dark:text-zinc-300 max-h-[420px] overflow-auto">{{ $promptData['system'] }}</pre>
                            <pre x-show="vtab === 'message'" x-cloak class="whitespace-pre-wrap p-5 font-mono text-sm leading-relaxed text-zinc-700 dark:text-zinc-300 max-h-[420px] overflow-auto">{{ $promptData['prompt'] }}</pre>
                            <div x-show="vtab !== 'system' && vtab !== 'message'" x-cloak></div>
                        </div>
                    </section>

                    {{-- Variables --}}
                    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                        <header class="border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Variables</h2>
                        </header>
                        @if (count($promptData['variables']))
                            <div class="grid gap-3 p-4 sm:grid-cols-2 sm:p-5">
                                @foreach ($promptData['variables'] as $variable)
                                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-white/10">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-mono text-[0.7rem] font-medium text-brand-600 dark:text-brand-400">&#123;&#123; {{ $variable['key'] }} &#125;&#125;</span>
                                            <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $variable['label'] }}</span>
                                        </div>
                                        <p class="mt-1.5 truncate rounded-md bg-zinc-50 px-2.5 py-1.5 font-mono text-xs text-zinc-600 dark:bg-zinc-950/40 dark:text-zinc-400">
                                            {{ $variable['default'] !== '' ? $variable['default'] : '(no default)' }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-4 pb-4">
                                <x-shared.empty-state icon="variable" title="No variables" description="This prompt doesn't use any variables." />
                            </div>
                        @endif
                    </section>

                    {{-- Latest run --}}
                    @if ($latestRun)
                        <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                            <header class="flex items-center justify-between border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Latest run</h2>
                                <flux:link href="{{ route('history.show', $latestRun['id']) }}" wire:navigate variant="subtle" size="sm" icon:trailing="arrow-right">View run</flux:link>
                            </header>
                            <div class="p-4 sm:p-5">
                                <div class="flex flex-wrap items-center gap-3">
                                    <x-shared.model-badge :slug="$latestRun['model']" :provider-slug="$latestRun['provider']" />
                                    <x-shared.status-badge :status="$latestRun['status']" />
                                    <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ number_format($latestRun['tokens']) }} tokens</span>
                                    @if ($latestRun['status'] === 'success')
                                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ \App\Support\MockData::latency($latestRun['latencyMs']) }}</span>
                                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ \App\Support\MockData::money($latestRun['cost']) }}</span>
                                    @endif
                                    <span class="ml-auto inline-flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                                        <flux:icon.clock class="size-3.5" />
                                        {{ \App\Support\MockData::timeAgo($latestRun['createdAt']) }}
                                    </span>
                                </div>
                            </div>
                        </section>
                    @endif
                </div>

                <div class="min-w-0 space-y-6">
                    {{-- Run config summary --}}
                    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                        <header class="border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Defaults</h2>
                        </header>
                        <dl class="divide-y divide-zinc-100 px-4 py-1 text-sm dark:divide-white/5">
                            <div class="flex items-center justify-between gap-4 py-2.5">
                                <dt class="text-xs text-zinc-400 dark:text-zinc-500">Temperature</dt>
                                <dd class="font-mono text-xs tabular-nums text-zinc-700 dark:text-zinc-300">{{ number_format($promptData['temperature'], 2) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4 py-2.5">
                                <dt class="text-xs text-zinc-400 dark:text-zinc-500">Top P</dt>
                                <dd class="font-mono text-xs tabular-nums text-zinc-700 dark:text-zinc-300">{{ number_format($promptData['topP'], 2) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4 py-2.5">
                                <dt class="text-xs text-zinc-400 dark:text-zinc-500">Max tokens</dt>
                                <dd class="font-mono text-xs tabular-nums text-zinc-700 dark:text-zinc-300">{{ number_format($promptData['maxTokens']) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4 py-2.5">
                                <dt class="text-xs text-zinc-400 dark:text-zinc-500">Created</dt>
                                <dd class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ \Illuminate\Support\Carbon::parse($promptData['createdAt'])->format('M j, Y') }}</dd>
                            </div>
                        </dl>
                        <div class="px-4 pb-4">
                            <flux:button href="{{ route('playground', ['prompt' => $promptData['id']]) }}" wire:navigate variant="primary" size="sm" icon="bolt" class="w-full justify-center">Open in playground</flux:button>
                        </div>
                    </section>

                    {{-- Tags --}}
                    @if (count($promptData['tags']))
                        <section class="rounded-xl border border-zinc-200 bg-white p-4 shadow-xs sm:p-5 dark:border-white/10 dark:bg-zinc-900/60">
                            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Tags</h2>
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach ($promptData['tags'] as $tag)
                                    <span class="rounded-md bg-zinc-100 px-2 py-0.5 font-mono text-xs text-zinc-600 dark:bg-white/5 dark:text-zinc-300">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Version timeline --}}
                    @if (count($versions))
                        <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                            <header class="flex items-center justify-between border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Versions</h2>
                                <flux:link href="{{ route('prompts.versions', $promptData['id']) }}" wire:navigate variant="subtle" size="sm" icon:trailing="arrow-right">All</flux:link>
                            </header>
                            <ol class="px-5 py-4">
                                @foreach (array_slice($versions, 0, 4) as $i => $version)
                                    <li class="relative flex gap-3 pb-4 last:pb-0">
                                        @if (! $loop->last)
                                            <span class="absolute left-[9px] top-7 h-[calc(100%-1.5rem)] w-px bg-zinc-200 dark:bg-white/10" aria-hidden="true"></span>
                                        @endif
                                        <span class="relative z-10 grid size-4.5 shrink-0 place-items-center rounded-full border-2 {{ $version['current'] ?? false ? 'border-brand-500 bg-white dark:bg-zinc-900' : 'border-zinc-300 dark:border-white/20' }}">
                                            <span class="size-1.5 rounded-full {{ $version['current'] ?? false ? 'bg-brand-500' : 'bg-zinc-300 dark:bg-white/20' }}"></span>
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">v{{ $version['number'] }}</span>
                                                @if ($version['current'] ?? false)
                                                    <x-shared.status-badge status="published" :dot="false" />
                                                @endif
                                                <span class="ml-auto text-xs text-zinc-400 dark:text-zinc-500">{{ \App\Support\MockData::timeAgo($version['createdAt']) }}</span>
                                            </div>
                                            <p class="mt-0.5 truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $version['note'] }} · {{ $version['author'] }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    @endif
</x-app.page-container>