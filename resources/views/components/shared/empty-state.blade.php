@props([
    'icon' => 'inbox',
    'title' => 'Nothing here yet',
    'description' => null,
    'compact' => false,
])

<div {{ $attributes->class(['flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-300 bg-white/50 text-center dark:border-white/10 dark:bg-white/[0.02]']) }}>
    <div class="{{ $compact ? 'px-4 py-8' : 'px-6 py-14' }}">
        <div class="mx-auto grid size-10 place-items-center rounded-lg bg-zinc-100 text-zinc-400 dark:bg-white/5 dark:text-zinc-500">
            <flux:icon :icon="$icon" class="size-5" />
        </div>
        <h3 class="mt-3 text-sm font-semibold text-zinc-800 dark:text-zinc-100">{{ $title }}</h3>
        @if ($description)
            <p class="mx-auto mt-1 max-w-sm text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
        @endif
        @isset($action)
            <div class="mt-4 flex justify-center">
                {{ $action }}
            </div>
        @endisset
    </div>
</div>