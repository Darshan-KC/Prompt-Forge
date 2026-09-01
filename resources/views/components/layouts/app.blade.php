@props(['title' => null, 'description' => null])

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

<body class="min-h-dvh font-sans antialiased bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
    <flux:sidebar collapsible>
        <x-app.sidebar />
    </flux:sidebar>

    <flux:header sticky class="border-b border-zinc-200 bg-zinc-50/90 backdrop-blur dark:border-white/10 dark:bg-zinc-950/80">
        <x-app.topbar />
    </flux:header>

    <flux:main class="min-w-0">
        {{ $slot }}
    </flux:main>

    <x-app.command-palette />

    <x-app.toaster />

    <flux:toast />

    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>
</body>
</html>