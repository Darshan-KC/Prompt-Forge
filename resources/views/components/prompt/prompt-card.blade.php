@props(['prompt' => []])

@php
    $categoryTones = [
        'Development' => 'bg-sky-500/10 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300',
        'Writing' => 'bg-violet-500/10 text-violet-700 dark:bg-violet-400/10 dark:text-violet-300',
        'Research' => 'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
        'Marketing' => 'bg-amber-500/10 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        'Education' => 'bg-rose-500/10 text-rose-700 dark:bg-rose-400/10 dark:text-rose-300',
        'Data Analysis' => 'bg-cyan-500/10 text-cyan-700 dark:bg-cyan-400/10 dark:text-cyan-300',
        default => 'bg-zinc-500/10 text-zinc-600 dark:bg-zinc-400/10 dark:text-zinc-300',
    ];
    $tone = $categoryTones[$prompt['category']] ?? $categoryTones['default'];
@endphp

<div
    x-data="{ fav: {{ Js::from($prompt['favorite']) }} }"
    class="group relative flex flex-col rounded-xl border border-zinc-200 bg-white p-4 shadow-xs transition duration-150 hover:border-zinc-300 hover:shadow-md dark:border-white/10 dark:bg-zinc-900/60 dark:hover:border-white/15"
>
    <div class="flex items-start justify-between gap-2">
        <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[0.65rem] font-semibold uppercase tracking-wider {{ $tone }}">
            {{ $prompt['category'] }}
        </span>

        <div class="flex items-center gap-0.5 opacity-60 transition group-hover:opacity-100">
            <a
                href="{{ route('playground', ['prompt' => $prompt['id']]) }}"
                wire:navigate
                class="inline-flex size-7 items-center justify-center rounded-md text-zinc-400 transition hover:bg-zinc-800/5 hover:text-zinc-700 dark:hover:bg-white/10 dark:hover:text-zinc-200"
                title="Open in playground"
                aria-label="Open {{ $prompt['name'] }} in playground"
            >
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

    <a href="{{ route('prompts.show', $prompt['id']) }}" wire:navigate class="mt-3 min-w-0">
        <h3 class="truncate text-sm font-semibold text-zinc-900 transition group-hover:text-brand-700 dark:text-zinc-100 dark:group-hover:text-brand-300">
            {{ $prompt['name'] }}
        </h3>
    </a>

    <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">
        {{ $prompt['description'] }}
    </p>

    <div class="mt-3 flex flex-wrap gap-1.5">
        @foreach (array_slice($prompt['tags'], 0, 3) as $tag)
            <span class="rounded-md bg-zinc-100 px-1.5 py-0.5 font-mono text-[0.65rem] text-zinc-500 dark:bg-white/5 dark:text-zinc-400">{{ $tag }}</span>
        @endforeach
        @if (count($prompt['variables']))
            <span class="inline-flex items-center gap-0.5 rounded-md border border-brand-500/20 bg-brand-500/5 px-1.5 py-0.5 font-mono text-[0.65rem] text-brand-700 dark:text-brand-300">
                <flux:icon.variable class="size-3" />
                {{ count($prompt['variables']) }} vars
            </span>
        @endif
    </div>

    <div class="mt-4 flex items-center justify-between gap-2 border-t border-zinc-100 pt-3 dark:border-white/5">
        <x-shared.model-badge :slug="$prompt['model']" :provider-slug="$prompt['provider']" class="max-w-[55%]" />
        <time class="text-xs text-zinc-400 dark:text-zinc-500" title="{{ $prompt['updatedAt'] }}">
            {{ \App\Support\MockData::timeAgo($prompt['updatedAt']) }}
        </time>
    </div>
</div>