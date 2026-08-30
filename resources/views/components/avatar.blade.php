@props([
    'user' => null,
    'name' => null,
    'class' => 'size-8',
    'theme' => null,
])

@php
    $name ??= $user?->name;
    $words = collect(explode(' ', (string) $name))->filter(fn ($w) => $w !== '');
    $initials = strtoupper(mb_substr($words->first() ?? 'P', 0, 1).mb_substr($words->last() ?? 'F', 0, 1));
@endphp

<div {{ $attributes->class([
    'grid shrink-0 place-items-center rounded-full bg-gradient-to-br font-semibold tracking-wide text-white',
    'from-brand-500 to-brand-800 ring-1 ring-inset ring-black/10 dark:ring-white/20',
    $class,
    $theme ?? 'text-[0.65rem]',
]) }} aria-hidden="true">
    {{ $initials }}
</div>