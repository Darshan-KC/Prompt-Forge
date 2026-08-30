@props(['prompt' => []])

@php
    $toast = fn (string $title, string $message, string $type = 'success') => sprintf(
        "window.dispatchEvent(new CustomEvent('pf-toast', { detail: %s }))",
        Js::from(['title' => $title, 'message' => $message, 'type' => $type])
    );
@endphp

<flux:dropdown>
    <button
        type="button"
        class="inline-flex size-7 items-center justify-center rounded-md text-zinc-400 transition hover:bg-zinc-800/5 hover:text-zinc-700 dark:hover:bg-white/10 dark:hover:text-zinc-200"
        aria-label="Actions for {{ $prompt['name'] }}"
    >
        <flux:icon.ellipsis-horizontal class="size-3.5" />
    </button>

    <flux:menu class="w-56 min-w-56">
        <div class="px-3 pb-1 pt-2">
            <div class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $prompt['name'] }}</div>
        </div>
        <flux:menu.group heading="Manage">
            <flux:menu.item icon="play" :href="route('playground', ['prompt' => $prompt['id']])" wire:navigate>Run</flux:menu.item>
            <flux:menu.item icon="pencil-square" :href="route('prompts.show', $prompt['id'])" wire:navigate>Edit</flux:menu.item>
            <flux:menu.item icon="arrows-right-left" :href="route('prompts.compare', $prompt['id'])" wire:navigate>Compare</flux:menu.item>
            <flux:menu.item icon="clock" :href="route('prompts.versions', $prompt['id'])" wire:navigate>Version history</flux:menu.item>
        </flux:menu.group>
        <flux:menu.separator />
        <flux:menu.item
            icon="squares-plus"
            x-on:click="{!! $toast('Prompt duplicated', 'A copy of “'.$prompt['name'].'” was added to your library.', 'success') !!}"
        >
            Duplicate
        </flux:menu.item>
        <flux:menu.item
            icon="trash"
            variant="danger"
            x-on:click="{!! $toast('Prompt archived', '“'.$prompt['name'].'” was archived.', 'info') !!}"
        >
            Move to archive
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>