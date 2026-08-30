<div
    x-data="{ dark: document.documentElement.classList.contains('dark') }"
    {{ $attributes->class('inline-flex') }}
>
    <button
        type="button"
        x-on:click="
            dark = !dark;
            document.documentElement.classList.toggle('dark', dark);
            localStorage.setItem('pf-theme', dark ? 'dark' : 'light');
        "
        x-bind:aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
        x-bind:title="dark ? 'Switch to light mode' : 'Switch to dark mode'"
        class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-800/5 hover:text-zinc-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
    >
        <flux:icon.sun x-cloak x-show="!dark" class="size-4.5" />
        <flux:icon.moon x-cloak x-show="dark" class="size-4.5" />
    </button>
</div>