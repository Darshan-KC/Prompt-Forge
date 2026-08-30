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
        ])->values()->all(),
        'system' => $promptData['system'],
        'message' => $promptData['prompt'],
    ] : null;
@endphp

<x-app.page-container class="space-y-6">
    @if (! $promptData)
        <div class="mx-auto max-w-sm py-20">
            <x-shared.empty-state icon="arrows-right-left" title="Prompt not found" description="This prompt doesn't exist in the mock library.">
                <flux:button href="{{ route('prompts.index') }}" wire:navigate variant="primary" size="sm">Back to library</flux:button>
            </x-shared.empty-state>
        </div>
    @else
        <div x-data="pfDiff({{ Js::from($initial) }})" x-cloak>
            <x-shared.page-header
                eyebrow="Version compare"
                :title="$promptData['name']"
                description="A word-for-word diff between any two versions of this prompt."
            >
                <x-slot:actions>
                    <flux:button variant="primary" icon="play" href="{{ route('playground', ['prompt' => $promptData['id']]) }}" wire:navigate>Run v{{ $promptData['version'] }}</flux:button>
                    <flux:link href="{{ route('prompts.versions', $promptData['id']) }}" wire:navigate variant="subtle">Version history</flux:link>
                </x-slot:actions>
            </x-shared.page-header>

            <div class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                {{-- Controls --}}
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
                            class="block max-w-[220px] cursor-pointer truncate rounded-lg border-0 bg-zinc-50 px-2.5 py-2 font-mono text-xs text-zinc-700 ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-950/40 dark:text-zinc-300 dark:ring-white/10">
                            <template x-for="v in versions" :key="v.number">
                                <option x-bind:value="v.number" x-text="'v' + v.number"></option>
                            </template>
                        </select>
                        <button type="button" x-on:click="const t = base; base = compare; compare = t"
                            class="grid size-8 shrink-0 place-items-center rounded-lg border border-zinc-200 text-zinc-400 transition hover:text-brand-600 dark:border-white/10 dark:text-zinc-500 dark:hover:text-brand-400" title="Swap versions" aria-label="Swap versions">
                            <flux:icon.arrows-right-left class="size-3.5" />
                        </button>
                        <select x-model.number="compare"
                            class="block max-w-[220px] cursor-pointer truncate rounded-lg border-0 bg-zinc-50 px-2.5 py-2 font-mono text-xs text-zinc-700 ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-950/40 dark:text-zinc-300 dark:ring-white/10">
                            <template x-for="v in versions" :key="v.number">
                                <option x-bind:value="v.number" x-text="'v' + v.number"></option>
                            </template>
                        </select>
                    </div>
                </header>

                {{-- Summary --}}
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 border-b border-zinc-100 px-4 py-3 text-xs text-zinc-500 sm:px-5 dark:border-white/5 dark:text-zinc-400">
                    <span class="inline-flex items-center gap-1.5">
                        <b class="font-semibold text-zinc-800 dark:text-zinc-200" x-text="'v' + baseInfo.number"></b>
                        <span x-text="baseInfo.note || 'Original experiment'"></span>
                    </span>
                    <flux:icon.arrow-right class="size-3.5 text-zinc-300 dark:text-zinc-600" aria-hidden="true" />
                    <span class="inline-flex items-center gap-1.5">
                        <b class="font-semibold text-zinc-800 dark:text-zinc-200" x-text="'v' + compareInfo.number"></b>
                        <span x-text="compareInfo.note || 'Original experiment'"></span>
                    </span>
                    <span class="ml-auto inline-flex items-center gap-3 tabular-nums">
                        <span class="inline-flex items-center gap-1 text-emerald-700 dark:text-emerald-300">
                            <b x-text="diffRows().filter(r => r.type === 'add').length"></b> added
                        </span>
                        <span class="inline-flex items-center gap-1 text-red-700 dark:text-red-300">
                            <b x-text="diffRows().filter(r => r.type === 'rem').length"></b> removed
                        </span>
                        <span class="inline-flex items-center gap-1 text-zinc-400 dark:text-zinc-500">
                            <b x-text="diffRows().filter(r => r.type === 'ctx').length"></b> unchanged
                        </span>
                    </span>
                </div>

                {{-- Side-by-side --}}
                <div class="overflow-x-auto">
                    <div class="grid min-w-[860px] grid-cols-2 divide-x divide-zinc-100 dark:divide-white/5">
                        <div>
                            <div class="sticky top-0 flex items-center gap-2 border-b border-zinc-100 bg-zinc-50/80 px-4 py-2 font-mono text-xs font-semibold text-zinc-500 backdrop-blur dark:border-white/5 dark:bg-zinc-950/50 dark:text-zinc-400">
                                <span class="rounded bg-zinc-200/70 px-1.5 py-0.5 dark:bg-white/10" x-text="'v' + baseInfo.number"></span>
                                <span class="truncate" x-text="baseInfo.note"></span>
                            </div>
                            <div class="px-4 py-3 font-mono text-[0.8rem] leading-relaxed">
                                <template x-for="(row, i) in diffRows()" :key="i">
                                    <div class="px-1.5 py-[2px]" :class="{
                                        'bg-red-500/[0.07] text-red-700 dark:text-red-300 line-through decoration-red-400/40': row.type === 'rem',
                                        'text-zinc-600 dark:text-zinc-300': row.type === 'ctx'
                                    }">
                                        <span class="whitespace-pre-wrap" x-text="row.type === 'rem' ? row.line : (row.type === 'add' ? '' : row.line)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <div class="sticky top-0 flex items-center gap-2 border-b border-zinc-100 bg-zinc-50/80 px-4 py-2 font-mono text-xs font-semibold text-zinc-500 backdrop-blur dark:border-white/5 dark:bg-zinc-950/50 dark:text-zinc-400">
                                <span class="rounded bg-zinc-200/70 px-1.5 py-0.5 dark:bg-white/10" x-text="'v' + compareInfo.number"></span>
                                <span class="truncate" x-text="compareInfo.note"></span>
                            </div>
                            <div class="px-4 py-3 font-mono text-[0.8rem] leading-relaxed">
                                <template x-for="(row, i) in diffRows()" :key="i">
                                    <div class="px-1.5 py-[2px]" :class="{
                                        'bg-emerald-500/[0.07] text-emerald-700 dark:text-emerald-300': row.type === 'add',
                                        'text-zinc-600 dark:text-zinc-300': row.type === 'ctx'
                                    }">
                                        <span class="whitespace-pre-wrap" x-text="row.type === 'add' ? row.line : (row.type === 'rem' ? '' : row.line)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app.page-container>