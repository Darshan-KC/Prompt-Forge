@props([
    'title' => null,
    'description' => null,
    'eyebrow' => null,
])

<div {{ $attributes->class('mx-auto w-full max-w-md px-4 py-12 sm:py-16') }}>
    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-white/10 dark:bg-zinc-900/60">
        <div class="h-1 bg-gradient-to-r from-brand-500 via-brand-600 to-brand-700" aria-hidden="true"></div>

        <div class="p-6 sm:p-8">
            @if ($eyebrow)
                <p class="text-xs font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">{{ $eyebrow }}</p>
            @endif

            @isset($title)
                <h1 class="mt-1 text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $title }}</h1>
            @endisset

            @if ($description)
                <p class="mt-1 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
            @endif

            <div class="{{ isset($title) || $description ? 'mt-6' : '' }}">
                {{ $slot }}
            </div>
        </div>
    </div>

    @isset($footer)
        <div class="mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
            {{ $footer }}
        </div>
    @endisset
</div>