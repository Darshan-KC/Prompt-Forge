@props([
    'label' => null,
    'value' => null,
    'delta' => null,
    'interval' => null,
    'icon' => null,
    'tone' => 'default',
])

@php
    $toneClasses = [
        'default' => 'text-zinc-700 dark:text-zinc-300',
        'brand' => 'text-brand-600 dark:text-brand-400',
        'emerald' => 'text-emerald-600 dark:text-emerald-400',
    ];
@endphp

<div {{ $attributes->class(['rounded-xl border border-zinc-200 bg-white p-4 shadow-xs dark:border-white/10 dark:bg-zinc-900/60']) }}>
    <div class="flex items-center gap-2">
        @if ($icon)
            <span class="text-zinc-400 dark:text-zinc-500">
                <flux:icon :icon="$icon" class="size-3.5" />
            </span>
        @endif
        <span class="text-[0.7rem] font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $label }}</span>
    </div>

    <div class="mt-2.5 flex items-baseline justify-between gap-2">
        <span class="text-2xl font-semibold tracking-tight {{ $toneClasses[$tone] }}">{{ $value }}</span>
        @if ($delta !== null)
            <span
                class="inline-flex items-center gap-0.5 text-xs font-medium {{ $delta >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-500 dark:text-zinc-400' }}"
                title="{{ $interval }}"
            >
                @if ($delta > 0)
                    <flux:icon.arrow-trending-up class="size-3.5" />
                    {{ $delta }}%
                @else
                    <flux:icon.arrow-trending-down class="size-3.5" />
                    {{ abs($delta) }}%
                @endif
            </span>
        @endif
    </div>

    @if ($interval)
        <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">{{ $interval }}</p>
    @endif
</div>