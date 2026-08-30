@props(['prompt' => []])

@php
    $categoryTone = [
        'Development' => 'text-sky-600 dark:text-sky-300',
        'Writing' => 'text-violet-600 dark:text-violet-300',
        'Research' => 'text-emerald-600 dark:text-emerald-300',
        'Marketing' => 'text-amber-600 dark:text-amber-300',
        'Education' => 'text-rose-600 dark:text-rose-300',
        'Data Analysis' => 'text-cyan-600 dark:text-cyan-300',
    ];
@endphp

<div
    x-data="{ fav: {{ Js::from($prompt['favorite']) }} }"
    class="group flex items-center gap-3 border-b border-zinc-100 px-4 py-3 transition last:border-0 hover:bg-zinc-800/[0.02] lg:px-5 dark:border-white/5 dark:hover:bg-white/[0.02]"
>
    <div class="min-w-0 flex-1">
        <div class="flex items-center gap-2">
            <a href="{{ route('prompts.show', $prompt['id']) }}" wire:navigate class="min-w-0 truncate text-sm font-medium text-zinc-900 transition group-hover:text-brand-700 dark:text-zinc-100 dark:group-hover:text-brand-300">
                {{ $prompt['name'] }}
            </a>
            @if ($prompt['status'] === 'draft')
                <x-shared.status-badge status="draft" :dot="false" />
            @endif
        </div>
        <div class="mt-0.5 flex items-center gap-2 truncate text-xs text-zinc-500 dark:text-zinc-400">
            <span class="truncate opacity-90">{{ $prompt['description'] }}</span>
            @if (count($prompt['variables']))
                <span class="hidden shrink-0 items-center gap-0.5 font-mono text-[0.65rem] text-brand-600 sm:inline-flex dark:text-brand-400">
                    <flux:icon.variable class="size-3" />
                    {{ count($prompt['variables']) }}
                </span>
            @endif
        </div>
    </div>

    <span class="hidden w-32 shrink-0 truncate text-xs font-medium {{ $categoryTone[$prompt['category']] ?? 'text-zinc-500 dark:text-zinc-400' }} md:block">
        {{ $prompt['category'] }}
    </span>

    <x-shared.model-badge :slug="$prompt['model']" :provider-slug="$prompt['provider']" class="hidden w-40 shrink-0 sm:inline-flex" />

    <span class="hidden w-20 shrink-0 text-right text-xs tabular-nums text-zinc-400 dark:text-zinc-500 lg:block">
        {{ \App\Support\MockData::timeAgo($prompt['updatedAt']) }}
    </span>

    <div class="flex shrink-0 items-center gap-0.5 opacity-60 transition group-hover:opacity-100">
        <a href="{{ route('playground', ['prompt' => $prompt['id']]) }}" wire:navigate class="inline-flex size-7 items-center justify-center rounded-md text-zinc-400 transition hover:bg-zinc-800/5 hover:text-zinc-700 dark:hover:bg-white/10 dark:hover:text-zinc-200" title="Open in playground">
            <flux:icon.play class="size-3.5" />
        </a>
        <button
            type="button"
            x-on:click="fav = !fav"
            x-bind:title="fav ? 'Remove from favorites' : 'Add to favorites'"
            x-bind:aria-label="fav ? 'Remove {{ $prompt['name'] }} from favorites' : 'Add {{ $prompt['name'] }} to favorites'"
            class="inline-flex size-7 items-center justify-center rounded-md text-zinc-400 transition hover:bg-zinc-800/5 hover:text-amber-500 dark:hover:bg-white/10"
        >
            <flux:icon.star x-cloak x-show="fav" variant="solid" class="size-3.5 text-amber-400" />
            <flux:icon.star x-cloak x-show="!fav" class="size-3.5" />
        </button>
        <x-app.prompt-menu :prompt="$prompt" />
    </div>
</div>