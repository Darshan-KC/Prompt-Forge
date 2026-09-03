@php
    $user = Auth::user();

    $isActive = fn (string ...$patterns) => request()->routeIs(...$patterns);

    $showFavorites = request()->query('favorites') === '1';
@endphp

<flux:sidebar.brand
    href="{{ route('dashboard') }}"
    name="prompt-forge"
    wire:navigate
>
    <x-logo class="size-5" />
</flux:sidebar.brand>

<flux:button
    variant="primary"
    icon:trailing="plus"
    href="{{ route('prompts.create') }}"
    wire:navigate
    class="h-9 w-full in-data-flux-sidebar-collapsed-desktop:w-10 in-data-flux-sidebar-collapsed-desktop:px-0 in-data-flux-sidebar-collapsed-desktop:justify-center"
>
    <span class="in-data-flux-sidebar-collapsed-desktop:hidden">
        New Prompt
    </span>
</flux:button>

<flux:sidebar.group heading="Workspace">
    <flux:navlist variant="outline">

        <flux:navlist.item
            icon="home"
            href="{{ route('dashboard') }}"
            wire:navigate
            :data-current="$isActive('dashboard')"
        >
            Dashboard
        </flux:navlist.item>

        <flux:navlist.item
            icon="bolt"
            href="{{ route('playground') }}"
            wire:navigate
            :data-current="$isActive('playground')"
        >
            Playground
        </flux:navlist.item>

        <flux:navlist.item
            icon="squares-2x2"
            href="{{ route('prompts.index') }}"
            wire:navigate
            :data-current="!$showFavorites && $isActive('prompts.*')"
        >
            Prompts
        </flux:navlist.item>

        <flux:navlist.item
            icon="folder"
            href="{{ route('projects.index') }}"
            wire:navigate
            :data-current="$isActive('projects.*')"
        >
            Projects
        </flux:navlist.item>

        <flux:navlist.item
            icon="clock"
            href="{{ route('history.index') }}"
            wire:navigate
            :data-current="$isActive('history.*')"
        >
            History
        </flux:navlist.item>

        <flux:navlist.item
            icon="chart-bar"
            href="{{ route('analytics') }}"
            wire:navigate
            :data-current="$isActive('analytics')"
        >
            Analytics
        </flux:navlist.item>

        <flux:navlist.item
            icon="star"
            href="{{ route('prompts.index', ['favorites' => 1]) }}"
            wire:navigate
            :data-current="$showFavorites"
        >
            Favorites
        </flux:navlist.item>

    </flux:navlist>
</flux:sidebar.group>

<flux:sidebar.group heading="Manage">
    <flux:navlist variant="outline">

        <flux:navlist.item
            icon="cog-6-tooth"
            href="{{ route('settings.profile') }}"
            wire:navigate
            :data-current="$isActive('settings.*')"
        >
            Settings
        </flux:navlist.item>

    </flux:navlist>
</flux:sidebar.group>

<flux:sidebar.spacer />

<flux:dropdown position="top" align="start">

    <flux:sidebar.profile :name="$user->name">
        <x-slot:avatar>
            <x-avatar
                :user="$user"
                class="size-7 text-[0.58rem]"
            />
        </x-slot:avatar>
    </flux:sidebar.profile>

    <flux:menu class="w-56 min-w-56">

        <div class="px-3 py-2.5">
            <div class="truncate text-sm font-medium text-zinc-900 dark:text-white">
                {{ $user->name }}
            </div>

            <div class="truncate text-xs text-zinc-500 dark:text-zinc-400">
                {{ $user->email }}
            </div>
        </div>

        <flux:menu.separator />

        <flux:menu.item
            icon="user-circle"
            href="{{ route('settings.profile') }}"
            wire:navigate
        >
            Profile
        </flux:menu.item>

        <flux:menu.item
            icon="cog-6-tooth"
            href="{{ route('settings.profile') }}"
            wire:navigate
        >
            Settings
        </flux:menu.item>

        <flux:menu.item
            icon="arrow-right-start-on-rectangle"
            href="{{ route('settings.account') }}"
            wire:navigate
        >
            Account
        </flux:menu.item>

        <flux:menu.separator />

        <flux:menu.item
            icon="arrow-right-start-on-rectangle"
            variant="danger"
            href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
        >
            Sign out
        </flux:menu.item>

    </flux:menu>
</flux:dropdown>