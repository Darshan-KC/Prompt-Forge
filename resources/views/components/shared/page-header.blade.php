@props([
    'title' => null,
    'description' => null,
    'eyebrow' => null,
    'kbd' => null,
])

<div {{ $attributes->class('flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between') }}>
    <div class="min-w-0">
        @if ($eyebrow)
            <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">{{ $eyebrow }}</p>
        @endif

        <div class="flex items-center gap-2.5">
            @isset($title)
                <h1 class="truncate text-xl font-semibold tracking-tight text-zinc-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            @else
                {{ $slot }}
            @endisset

            @if ($kbd)
                <kbd title="Keyboard shortcut" class="hidden items-center rounded border border-zinc-200 bg-white px-1.5 py-0.5 font-mono text-[0.65rem] text-zinc-400 sm:flex dark:border-white/10 dark:bg-white/5 dark:text-zinc-500">{{ $kbd }}</kbd>
            @endif
        </div>

        @if ($description)
            <p class="mt-1 max-w-2xl text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex shrink-0 items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>