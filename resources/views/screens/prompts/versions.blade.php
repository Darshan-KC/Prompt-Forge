<x-layouts.app>
@php
    $promptId = is_numeric($prompt ?? null) ? (int) $prompt : null;
    $promptData = $promptId !== null && $promptId > 0 ? \App\Support\MockData::prompt($promptId) : null;
    $versions = $promptData ? \App\Support\MockData::versionsFor($promptData['id']) : [];

    $initial = $promptData ? [
        'name' => $promptData['name'],
        'currentVersion' => $promptData['version'],
        'versions' => collect($versions)->map(fn ($v) => [
            'number' => $v['number'],
            'note' => $v['note'],
            'current' => $v['current'] ?? false,
            'author' => $v['author'],
            'createdAt' => $v['createdAt'],
        ])->values()->all(),
        'system' => $promptData['system'],
        'message' => $promptData['prompt'],
    ] : null;
@endphp

<x-app.page-container class="space-y-6">
    @if (! $promptData)
        <div class="mx-auto max-w-sm py-20">
            <x-shared.empty-state icon="rectangle-stack" title="Prompt not found" description="This prompt doesn't exist in the mock library.">
                <flux:button href="{{ route('prompts.index') }}" wire:navigate variant="primary" size="sm">Back to library</flux:button>
            </x-shared.empty-state>
        </div>
    @else
        <div x-data="pfDiff({{ Js::from($initial) }})" x-cloak>
            <x-shared.page-header
                eyebrow="Version history"
                :title="$promptData['name']"
                description="Every edit is a version — restore, compare and trace exactly what changed."
            >
                <x-slot:actions>
                    <flux:button variant="primary" icon="play" href="{{ route('playground', ['prompt' => $promptData['id']]) }}" wire:navigate>Run v{{ $promptData['version'] }}</flux:button>
                    <flux:link href="{{ route('prompts.show', $promptData['id']) }}" wire:navigate variant="subtle">← Back to prompt</flux:link>
                </x-slot:actions>
            </x-shared.page-header>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
                {{-- Diff --}}
                <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                    <header class="flex flex-col gap-3 border-b border-zinc-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-5 dark:border-white/5">
                        <div class="flex items-center gap-1 rounded-lg bg-zinc-100 p-1 dark:bg-white/5">
                            <button type="button" x-on:click="tab = 'message'"
                                class="rounded-md px-3 py-1 text-xs font-medium transition"
                                :class="tab === 'message' ? 'bg-white text-zinc-900 shadow-xs dark:bg-zinc-800 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'">Message</button>
                            <button type="button" x-on:click="tab = 'system'"
                                class="rounded-md px-3 py-1 text-xs font-medium transition"
                                :class="tab === 'system' ? 'bg-white text-zinc-900 shadow-xs dark:bg-zinc-800 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'">System</button>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <select x-model.number="base"
                                class="block cursor-pointer rounded-lg border-0 bg-zinc-50 px-2.5 py-1.5 font-mono text-xs text-zinc-700 ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-950/40 dark:text-zinc-300 dark:ring-white/10">
                                <template x-for="v in versions" :key="v.number">
                                    <option x-bind:value="v.number" x-text="'v' + v.number + ' — ' + v.note"></option>
                                </template>
                            </select>
                            <span class="text-zinc-400 dark:text-zinc-500">→</span>
                            <select x-model.number="compare"
                                class="block cursor-pointer rounded-lg border-0 bg-zinc-50 px-2.5 py-1.5 font-mono text-xs text-zinc-700 ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-950/40 dark:text-zinc-300 dark:ring-white/10">
                                <template x-for="v in versions" :key="v.number">
                                    <option x-bind:value="v.number" x-text="'v' + v.number + ' — ' + v.note"></option>
                                </template>
                            </select>
                        </div>
                    </header>

                    <div class="flex items-center justify-between border-b border-zinc-100 px-4 py-2 text-xs text-zinc-400 sm:px-5 dark:border-white/5">
                        <span class="inline-flex items-center gap-1">
                            Comparing
                            <b class="font-semibold text-zinc-600 dark:text-zinc-300" x-text="'v' + baseInfo.number"></b>
                            (<span x-text="baseInfo.note"></span>)
                            →
                            <b class="font-semibold text-zinc-600 dark:text-zinc-300" x-text="'v' + compareInfo.number"></b>
                        </span>
                        <span class="tabular-nums"><b x-text="changedCount()" class="font-semibold text-zinc-600 dark:text-zinc-300"></b> changed lines</span>
                    </div>

                    <div class="max-h-[560px] overflow-auto p-0 font-mono text-[0.8rem] leading-relaxed">
                        <template x-for="(row, i) in diffRows()" :key="i">
                            <div class="flex min-w-max items-start gap-2.5 border-b border-zinc-100/60 px-4 py-[3px] sm:px-5 dark:border-white/[0.03]"
                                :class="{
                                    'bg-emerald-500/[0.06]': row.type === 'add',
                                    'bg-red-500/[0.06]': row.type === 'rem'
                                }">
                                <span class="select-none w-5 shrink-0 text-right text-zinc-300 dark:text-zinc-600"
                                    x-text="row.type === 'add' ? '+' : (row.type === 'rem' ? '−' : '')"></span>
                                <span class="whitespace-pre-wrap text-zinc-500 dark:text-zinc-400"
                                    :class="{
                                        'text-emerald-700 dark:text-emerald-300': row.type === 'add',
                                        'text-red-700 dark:text-red-300': row.type === 'rem',
                                        'text-zinc-600 dark:text-zinc-300': row.type === 'ctx'
                                    }" x-text="row.line"></span>
                            </div>
                        </template>
                    </div>
                </section>

                {{-- History list --}}
                <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                    <header class="border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">All versions</h2>
                    </header>
                    <ol class="px-5 py-4">
                        @foreach ($versions as $version)
                            <li class="relative flex gap-3 pb-5 last:pb-0">
                                @if (! $loop->last)
                                    <span class="absolute left-[11px] top-8 h-[calc(100%-1.75rem)] w-px bg-zinc-200 dark:bg-white/10" aria-hidden="true"></span>
                                @endif
                                <span class="relative z-10 grid size-5 shrink-0 place-items-center rounded-full border-2 {{ $version['current'] ?? false ? 'border-brand-500 bg-white dark:bg-zinc-900' : 'border-zinc-300 dark:border-white/20' }}">
                                    <span class="size-1.5 rounded-full {{ $version['current'] ?? false ? 'bg-brand-500' : 'bg-zinc-300 dark:bg-white/20' }}"></span>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <button type="button" x-on:click="compare = {{ $version['number'] }}" class="text-xs font-semibold text-zinc-800 hover:text-brand-600 dark:text-zinc-200 dark:hover:text-brand-400">v{{ $version['number'] }}</button>
                                        @if ($version['current'] ?? false)
                                            <x-shared.status-badge status="published" :dot="false" />
                                        @endif
                                        <span class="ml-auto text-xs text-zinc-400 dark:text-zinc-500">{{ \App\Support\MockData::timeAgo($version['createdAt']) }}</span>
                                    </div>
                                    <p class="mt-0.5 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $version['note'] }}</p>
                                    <p class="mt-0.5 text-[0.7rem] text-zinc-400 dark:text-zinc-500">{{ $version['author'] }}</p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <flux:button variant="subtle" size="sm" x-on:click="$dispatch('pf-toast', { icon: 'check-circle', message: 'Restored v{{ $version['number'] }} (mock).' })" :disabled="$version['current'] ?? false">
                                            Restore
                                        </flux:button>
                                        <flux:link href="{{ route('prompts.compare', $promptData['id']) }}" wire:navigate variant="subtle" size="sm">Compare</flux:link>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>
            </div>
        </div>
    @endif
</x-app.page-container>
</x-layouts.app>
