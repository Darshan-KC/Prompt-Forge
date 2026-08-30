@php
    $menus = [
        'profile' => ['label' => 'Profile', 'icon' => 'user-circle', 'description' => 'Your personal information'],
        'appearance' => ['label' => 'Appearance', 'icon' => 'paint-brush', 'description' => 'Theme, density and language'],
        'providers' => ['label' => 'AI providers', 'icon' => 'server-stack', 'description' => 'Connect OpenAI, Anthropic and more'],
        'models' => ['label' => 'Models', 'icon' => 'cpu-chip', 'description' => 'Which models are available to run'],
        'api-keys' => ['label' => 'API keys', 'icon' => 'key', 'description' => 'Keys used for run billing and access'],
        'notifications' => ['label' => 'Notifications', 'icon' => 'bell', 'description' => 'Email and in-app notifications'],
        'account' => ['label' => 'Account', 'icon' => 'shield-check', 'description' => 'Security, teams and data'],
    ];
    $section = $section ?? 'profile';
    $active = $menus[$section] ?? $menus['profile'];
@endphp

<x-app.page-container class="space-y-6">
    <x-shared.page-header
        eyebrow="Settings"
        title="{{ $active['label'] }}"
        description="{{ $active['description'] }}"
    />

    @if (! in_array($section, array_keys($menus)))
        <x-shared.empty-state icon="cog-6-tooth" title="Section not found" description="That settings tab doesn't exist.">
            <flux:button href="{{ route('settings.profile') }}" wire:navigate variant="primary" size="sm">Go to profile</flux:button>
        </x-shared.empty-state>
    @else
        <div class="grid gap-8 lg:grid-cols-[220px_minmax(0,1fr)]">
            <nav aria-label="Settings tabs" class="lg:sticky lg:top-24 lg:self-start">
                <ul class="flex gap-1 overflow-x-auto pb-1 lg:flex-col lg:gap-1 lg:overflow-visible lg:pb-0">
                    @foreach ($menus as $slug => $menu)
                        <li class="shrink-0">
                            <a href="{{ route('settings.'.$slug) }}" wire:navigate
                                class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition lg:min-w-[200px]"
                                :class="{{ $section === $slug ? 'true' : 'false' }} ? 'bg-zinc-100 text-zinc-900 dark:bg-white/10 dark:text-white' : 'text-zinc-600 hover:bg-zinc-100/70 dark:text-zinc-400 dark:hover:bg-white/5'"
                                aria-current="{{ $section === $slug ? 'page' : null }}">
                                <span :class="{{ $section === $slug ? 'true' : 'false' }} ? 'text-brand-600 dark:text-brand-400' : 'text-zinc-400 dark:text-zinc-500'">
                                    <flux:icon :icon="$menu['icon']" class="size-4" />
                                </span>
                                {{ $menu['label'] }}
                                @if ($section === $slug)
                                    <flux:icon.chevron-right class="ml-auto hidden size-3.5 text-brand-500 lg:block" />
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="min-w-0">
                @include('screens.settings.'.$section)
            </div>
        </div>
    @endif
</x-app.page-container>