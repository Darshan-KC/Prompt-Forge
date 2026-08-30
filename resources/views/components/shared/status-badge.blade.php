@props([
    'status' => 'success',
    'dot' => true,
])

@php
    $config = \App\Support\MockData::statusConfig($status);
@endphp

<span {{ $attributes->class(['inline-flex w-fit items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium', $config['classes']]) }}>
    @if ($dot)
        <span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>
    @endif
    {{ $config['label'] }}
</span>