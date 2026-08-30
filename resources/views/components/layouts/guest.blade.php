<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="scroll-smooth"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Prompt-Forge') }}</title>
    <meta name="description" content="{{ $description ?? 'Prompt-Forge — a professional AI prompt engineering workspace.' }}">

    <script>
        (function () {
            function current() {
                var stored = localStorage.getItem('pf-theme');
                if (stored === 'light' || stored === 'dark') return stored;
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            if (current() === 'dark') document.documentElement.classList.add('dark');
            window.pfTheme = {
                current: current,
                apply: function (t) {
                    document.documentElement.classList.toggle('dark', t === 'dark');
                    localStorage.setItem('pf-theme', t);
                },
                toggle: function () {
                    window.pfTheme.apply(current() === 'dark' ? 'light' : 'dark');
                },
            };
        })();
    </script>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-dvh flex-col bg-zinc-50 font-sans text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    {{-- Shared guest chrome: product nav + footer around the page slot --}}

    <header class="sticky top-0 z-30 border-b border-zinc-200/80 bg-zinc-50/85 backdrop-blur dark:border-white/10 dark:bg-zinc-950/80" x-data="{ mobileOpen: false }">
        <x-app.page-container class="flex h-16 items-center justify-between gap-4 py-0!">
            <x-logo-wordmark href="{{ route('welcome') }}" />

            <nav class="hidden items-center gap-1 md:flex" aria-label="Product">
                <a href="{{ route('welcome').'#features' }}" wire:navigate.hover class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-800/5 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white">Features</a>
                <a href="{{ route('welcome').'#workflow' }}" wire:navigate.hover class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-800/5 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white">How it works</a>
                <a href="{{ route('welcome').'#pricing' }}" wire:navigate.hover class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-800/5 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white">Pricing</a>
            </nav>

            <div class="flex items-center gap-1.5">
                <x-theme-toggle class="hidden sm:inline-flex" />

                @auth
                    <flux:button variant="primary" href="{{ route('dashboard') }}" icon="arrow-up-right" wire:navigate>Open workspace</flux:button>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-lg px-3 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-800/5 hover:text-zinc-900 sm:block dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white">Log in</a>
                    <flux:button variant="primary" href="{{ route('register') }}" icon="arrow-up-right" wire:navigate>Get started</flux:button>
                @endauth

                <button
                    type="button"
                    class="inline-flex size-9 items-center justify-center rounded-lg text-zinc-600 hover:bg-zinc-800/5 md:hidden dark:text-zinc-400 dark:hover:bg-white/10"
                    x-on:click="mobileOpen = !mobileOpen"
                    x-bind:aria-expanded="mobileOpen"
                    aria-controls="guest-mobile-nav"
                    aria-label="Toggle navigation"
                >
                    <flux:icon.bars-3 x-cloak x-show="!mobileOpen" class="size-5" />
                    <flux:icon.x-mark x-cloak x-show="mobileOpen" class="size-5" />
                </button>
            </div>
        </x-app.page-container>

        <div
            x-cloak
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            id="guest-mobile-nav"
            class="border-t border-zinc-200/80 dark:border-white/10"
        >
            <x-app.page-container class="flex flex-col gap-1 py-3!">
                <a href="{{ route('welcome').'#features' }}" x-on:click="mobileOpen = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-800/5 dark:text-zinc-300 dark:hover:bg-white/10">Features</a>
                <a href="{{ route('welcome').'#workflow' }}" x-on:click="mobileOpen = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-800/5 dark:text-zinc-300 dark:hover:bg-white/10">How it works</a>
                <a href="{{ route('welcome').'#pricing' }}" x-on:click="mobileOpen = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-800/5 dark:text-zinc-300 dark:hover:bg-white/10">Pricing</a>
                <div class="mt-1 flex items-center justify-between border-t border-zinc-200/80 py-2 dark:border-white/10">
                    <x-theme-toggle />
                    @auth
                        <flux:button variant="primary" href="{{ route('dashboard') }}" wire:navigate>Open workspace</flux:button>
                    @else
                        <flux:button variant="primary" href="{{ route('register') }}" wire:navigate>Get started</flux:button>
                    @endauth
                </div>
            </x-app.page-container>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-zinc-200/80 dark:border-white/10">
        <x-app.page-container class="py-10!">
            <div class="flex flex-col justify-between gap-8 md:flex-row">
                <div class="max-w-sm">
                    <x-logo-wordmark href="{{ route('welcome') }}" logoClass="size-7" />
                    <p class="mt-3 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">
                        A focused workspace for engineering, iterating and observing AI prompts across every provider.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-8 sm:grid-cols-3">
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Product</h3>
                        <ul class="mt-3 space-y-2 text-sm">
                            <li><a href="{{ route('welcome').'#features' }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Features</a></li>
                            <li><a href="{{ route('welcome').'#pricing' }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Pricing</a></li>
                            <li><a href="{{ route('playground') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Playground</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Company</h3>
                        <ul class="mt-3 space-y-2 text-sm">
                            <li><a href="#" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">About</a></li>
                            <li><a href="#" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Blog</a></li>
                            <li><a href="#" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Status</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Legal</h3>
                        <ul class="mt-3 space-y-2 text-sm">
                            <li><a href="#" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Privacy</a></li>
                            <li><a href="#" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Terms</a></li>
                            <li><a href="#" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Security</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-zinc-200/80 pt-6 text-xs text-zinc-400 sm:flex-row dark:border-white/10 dark:text-zinc-500">
                <p>&copy; {{ date('Y') }} Prompt-Forge. Crafted with precision.</p>
                <p class="flex items-center gap-1.5">
                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                    All systems nominal
                </p>
            </div>
        </x-app.page-container>
    </footer>
</body>
</html>