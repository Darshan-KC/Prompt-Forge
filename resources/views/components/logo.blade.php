@props(['class' => 'size-9'])

{{--
    Prompt-Forge mark: a terminal prompt chevron (❯) struck with an ember
    forge tile. "Prompt" + "Forge" in one glyph, no robots, no sparkles.

    Usage:         <x-logo class="size-8" />
    Full wordmark: <x-logo.wordmark />
--}}

<div {{ $attributes->class([
    'relative shrink-0 overflow-hidden rounded-[10px] bg-gradient-to-br from-brand-500 via-brand-600 to-brand-800',
    'shadow-sm ring-1 ring-inset ring-black/10 dark:ring-white/20',
    $class,
]) }} aria-hidden="true">
    <svg viewBox="0 0 24 24" fill="none" class="absolute inset-0 size-full p-1.5 text-white">
        <path d="M8.6 8 13 12l-4.4 4" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M14.2 16h2.9" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" class="opacity-90"/>
    </svg>
    <div class="absolute inset-0 rounded-[10px] shadow-[inset_0_1px_0_rgba(255,255,255,0.25)]" aria-hidden="true"></div>
</div>