@php
    $prompts = \App\Support\MockData::prompts();
    $categories = \App\Support\MockData::categories();
    $folders = \App\Support\MockData::folders();
    $favoriteCount = collect($prompts)->where('favorite', true)->count();
    $folderPromptCount = fn (int $id) => collect($prompts)->where('folder', $id)->count();
@endphp

<x-app.page-container class="space-y-6">
    <x-shared.page-header
        eyebrow="Prompt library"
        title="Prompts"
        description="Your reusable prompt engineering artifacts, organized by folder and tag."
    >
        <x-slot:actions>
            <flux:button href="{{ route('prompts.create') }}" wire:navigate variant="primary" icon="plus">New prompt</flux:button>
        </x-slot:actions>
    </x-shared.page-header>

    <div x-data="pfLibrary({{ Js::from($prompts) }})" x-cloak>
        {{-- Toolbar --}}
        <div class="space-y-3">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <flux:icon.magnifying-glass class="pointer-events-none absolute start-3.5 top-1/2 size-4 -translate-y-1/2 text-zinc-400 dark:text-zinc-500" />
                    <input type="search" x-model="q"
                        placeholder="Search prompts, tags, descriptions…"
                        class="block w-full rounded-lg border-0 bg-white py-2.5 ps-10 pe-10 text-sm text-zinc-800 shadow-xs ring-1 ring-inset ring-zinc-200 transition placeholder:text-zinc-400 focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-900/60 dark:text-zinc-200 dark:ring-white/10">
                    <button type="button" x-show="q" x-on:click="q = ''"
                        class="absolute end-2 top-1/2 -translate-y-1/2 rounded p-1 text-zinc-400 transition hover:text-zinc-600 dark:hover:text-zinc-200" aria-label="Clear search">
                        <flux:icon.x-mark class="size-4" />
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <select x-model="sort"
                        class="block cursor-pointer rounded-lg border-0 bg-white px-3 py-2.5 text-sm text-zinc-700 shadow-xs ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-900/60 dark:text-zinc-300 dark:ring-white/10">
                        <option value="updated">Recently updated</option>
                        <option value="name">Name (A–Z)</option>
                        <option value="usage">Most used</option>
                    </select>

                    <button type="button" x-on:click="favs = !favs"
                        class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2.5 text-sm font-medium transition"
                        :class="favs
                            ? 'border-amber-400/50 bg-amber-500/10 text-amber-700 ring-1 ring-amber-400/30 dark:text-amber-300'
                            : 'border-zinc-200 bg-white text-zinc-600 shadow-xs dark:border-white/10 dark:bg-zinc-900/60 dark:text-zinc-400'">
                        <flux:icon.star class="size-3.5" x-bind:variant="favs ? 'solid' : 'outline'" :class="favs ? 'text-amber-500' : ''" />
                        Favorites
                        <span class="rounded-full px-1.5 text-xs tabular-nums" :class="favs ? 'bg-amber-500/20' : 'bg-zinc-100 dark:bg-white/10'">{{ $favoriteCount }}</span>
                    </button>
                </div>
            </div>

            {{-- Category chips --}}
            <div class="flex flex-wrap items-center gap-1.5">
                <button type="button" x-on:click="cat = 'all'"
                    class="rounded-full px-3 py-1 text-xs font-medium transition"
                    :class="cat === 'all' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10'">
                    All
                </button>
                @foreach ($categories as $category)
                    <button type="button" x-on:click="cat = '{{ $category }}'"
                        class="rounded-full px-3 py-1 text-xs font-medium transition"
                        :class="cat === '{{ $category }}' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-white/5 dark:text-zinc-400 dark:hover:bg-white/10'">
                        {{ $category }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Result count + folders --}}
        <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
            <p class="text-xs text-zinc-400 dark:text-zinc-500" x-show="visibleCount() > 0">
                Showing <span x-text="visibleCount()"></span> of {{ count($prompts) }} prompts
                <template x-if="favs"><span class="text-amber-500">(favorites only)</span></template>
            </p>
            <div class="flex flex-wrap items-center gap-1.5">
                @foreach ($folders as $folder)
                    <span class="inline-flex items-center gap-1.5 rounded-md border border-zinc-200 bg-white px-2 py-1 text-xs text-zinc-500 shadow-xs dark:border-white/10 dark:bg-zinc-900/60 dark:text-zinc-400" title="{{ $folder['name'] }} ({{ $folderPromptCount($folder['id']) }} prompts)">
                        <span class="size-1.5 rounded-full" style="background-color: {{ $folder['color'] }}"></span>
                        {{ $folder['name'] }}
                        <span class="tabular-nums text-zinc-400 dark:text-zinc-500">{{ $folderPromptCount($folder['id']) }}</span>
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Grid --}}
        <div x-show="visibleCount() > 0" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3" x-cloak>
            @foreach ($prompts as $i => $prompt)
                <div x-show="filtered({{ $i }})" :style="'order:' + orderOf({{ $i }})" x-cloak>
                    <x-prompt.prompt-card :prompt="$prompt" class="h-full" />
                </div>
            @endforeach
        </div>

        {{-- Empty state --}}
        <div x-show="visibleCount() === 0" x-cloak class="mt-4">
            <x-shared.empty-state
                icon="magnifying-glass"
                title="{{ $prompts ? 'No prompts match your filters' : 'No prompts yet' }}"
                description="Adjust the search or filters, or create a new prompt to get started."
            >
                @if ($prompts)
                    <flux:button variant="subtle" size="sm" x-on:click="q = ''; cat = 'all'; favs = false">Clear filters</flux:button>
                @else
                    <flux:button variant="primary" size="sm" href="{{ route('prompts.create') }}" wire:navigate icon="plus">New prompt</flux:button>
                @endif
            </x-shared.empty-state>
        </div>
    </div>
</x-app.page-container>