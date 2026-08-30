@props([
    'slug' => null,
    'providerSlug' => null,
])

@php
    $model = $providerSlug
        ? \App\Support\MockData::model($slug, $providerSlug)
        : \App\Support\MockData::model($slug);
    $model ??= ['name' => $slug, 'provider' => $providerSlug];
    $color = $model['provider'] ? \App\Support\MockData::providerColor($model['provider']) : null;
@endphp

<span {{ $attributes->class(['inline-flex w-fit max-w-full items-center gap-1.5 truncate rounded-md border border-zinc-200 bg-white px-2 py-0.5 font-mono text-[0.7rem] font-medium text-zinc-700 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300']) }} title="{{ $model['name'] }}">
    @if ($color)
        <span class="size-1.5 shrink-0 rounded-full" style="background-color: {{ $color }}" aria-hidden="true"></span>
    @endif
    <span class="truncate">{{ $model['name'] }}</span>
</span>