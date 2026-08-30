<a {{ $attributes->class('inline-flex items-center gap-2.5') }} href="{{ $href ?? route('welcome') }}" aria-label="{{ $name ?? 'Home — prompt-forge' }}" wire:navigate.hover>
    <x-logo class="{{ $logoClass ?? 'size-8' }}" />
    <span class="{{ $textClass ?? '' }} text-[0.95rem] font-semibold tracking-tight text-zinc-900 dark:text-white">
        prompt<span class="text-brand-600 dark:text-brand-400">-</span>forge
        @if (! empty($tag) && $tag === 'pro')
            <span class="ml-1 align-middle rounded-md bg-brand-500/15 px-1 py-0.5 text-[0.6rem] font-bold tracking-widest text-brand-700 dark:text-brand-300">PRO</span>
        @endif
    </span>
</a>