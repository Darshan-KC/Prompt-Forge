@props([
    'text' => '',
    'label' => 'Copy',
    'size' => 'icon',
])

@php
    $classes = match ($size) {
        'sm' => 'inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium',
        'default' => 'inline-flex size-7 items-center justify-center rounded-md',
    };
@endphp

<button
    type="button"
    x-data="{ copied: false }"
    x-on:click="
        navigator.clipboard?.writeText({{ Js::from($text) }});
        copied = true;
        setTimeout(() => (copied = false), 1600);
    "
    x-bind:aria-label="copied ? 'Copied' : '{{ $label }}'"
    x-bind:title="copied ? 'Copied' : '{{ $label }}'"
    {{ $attributes->class([
        $classes,
        'shrink-0 text-zinc-400 transition hover:bg-zinc-800/5 hover:text-zinc-700 dark:text-zinc-500 dark:hover:bg-white/10 dark:hover:text-zinc-200',
    ]) }}
>
    <flux:icon x-cloak x-show="!copied" class="size-4" icon="clipboard-document-check" />
    <flux:icon x-cloak x-show="copied" class="size-4 text-emerald-500" icon="check" />
</button>