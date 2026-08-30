@php
    $notifications = [
        ['id' => 1, 'icon' => 'check-circle', 'color' => 'text-emerald-500', 'title' => 'Run completed', 'body' => 'Code Review Assistant finished in 1.8s.', 'at' => '4m ago', 'unread' => true],
        ['id' => 2, 'icon' => 'arrow-path', 'color' => 'text-brand-500', 'title' => 'Version restored', 'body' => 'SQL Explain Optimizer restored to v4.', 'at' => '1h ago', 'unread' => true],
        ['id' => 3, 'icon' => 'exclamation-triangle', 'color' => 'text-amber-500', 'title' => 'Rate limit hit', 'body' => 'gpt-4o-mini requests throttled for 32s.', 'at' => '3h ago', 'unread' => true],
        ['id' => 4, 'icon' => 'check-circle', 'color' => 'text-emerald-500', 'title' => 'Settings updated', 'body' => 'Your default model was updated.', 'at' => 'Yesterday', 'unread' => false],
    ];
@endphp

<div class="flex w-full items-center gap-2 lg:gap-3">
    <flux:sidebar.toggle class="max-lg:[&>svg]:size-4.5!" />

    <div class="min-w-0 flex-1">
        <button
            type="button"
            x-on:click="$dispatch('open-command-palette')"
            class="group hidden w-full max-w-xs items-center gap-2 rounded-lg border border-zinc-200 bg-white/70 px-3 py-1.5 text-sm text-zinc-500 shadow-xs transition hover:border-zinc-300 hover:bg-white hover:text-zinc-700 sm:flex dark:border-white/10 dark:bg-white/5 dark:text-zinc-400 dark:hover:border-white/15 dark:hover:bg-white/10 dark:hover:text-zinc-200"
            aria-label="Open command palette (Ctrl K)"
        >
            <flux:icon.magnifying-glass class="size-4 text-zinc-400" />
            <span class="flex-1 text-start">Search prompts, actions…</span>
            <kbd class="hidden items-center gap-0.5 rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 font-mono text-[0.65rem] font-medium text-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-zinc-500 lg:flex">
                ⌘K
            </kbd>
        </button>

        <button
            type="button"
            x-on:click="$dispatch('open-command-palette')"
            class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-800/5 hover:text-zinc-800 sm:hidden dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
            aria-label="Open command palette (Ctrl K)"
        >
            <flux:icon.magnifying-glass class="size-4.5" />
        </button>
    </div>

    <div class="flex shrink-0 items-center gap-1.5">
        <flux:dropdown align="end">
            <button
                type="button"
                class="relative inline-flex size-9 shrink-0 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-800/5 hover:text-zinc-800 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                aria-label="Notifications"
            >
                <flux:icon.bell class="size-4.5" />
                <span class="absolute right-2 top-2 size-1.5 rounded-full bg-brand-500 ring-2 ring-zinc-50 dark:bg-brand-400 dark:ring-zinc-950"></span>
            </button>

            <flux:menu class="w-80 min-w-80">
                <flux:menu.group heading="Notifications">
                    <div class="flex flex-col">
                        @foreach ($notifications as $notification)
                            <div class="flex items-start gap-3 px-3 py-2.5 {{ $notification['unread'] ? 'bg-brand-500/[0.04] dark:bg-white/[0.03]' : '' }}">
                                <flux:icon :icon="$notification['icon']" :class="$notification['color'].' size-4.5 mt-0.5 shrink-0'" />
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $notification['title'] }}</span>
                                        @if ($notification['unread'])
                                            <span class="size-1 shrink-0 rounded-full bg-brand-500 dark:bg-brand-400"></span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 line-clamp-2 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $notification['body'] }}</p>
                                    <time class="mt-1 block text-[0.7rem] font-medium text-zinc-400 dark:text-zinc-500">{{ $notification['at'] }}</time>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </flux:menu.group>
                <flux:menu.separator />
                <div class="px-3 py-2.5">
                    <flux:link href="#" variant="subtle" class="text-xs">View all notifications</flux:link>
                </div>
            </flux:menu>
        </flux:dropdown>

        <x-theme-toggle />

        <div class="mx-1.5 hidden h-5 w-px bg-zinc-200 dark:bg-white/10 sm:block"></div>

        <flux:dropdown align="end">
            <flux:button variant="subtle" square class="rounded-full! p-0!">
                <x-avatar :user="Auth::user()" class="size-8 text-[0.6rem]" />
            </flux:button>

            <flux:menu class="w-56 min-w-56">
                <div class="px-3 py-2.5">
                    <div class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ Auth::user()->email }}</div>
                </div>
                <flux:menu.separator />
                <flux:menu.item icon="user-circle" href="{{ route('settings.profile') }}" wire:navigate>Profile</flux:menu.item>
                <flux:menu.item icon="cog-6-tooth" href="{{ route('settings.profile') }}" wire:navigate>Settings</flux:menu.item>
                <flux:menu.item icon="arrow-right-start-on-rectangle" href="{{ route('settings.account') }}" wire:navigate>Account</flux:menu.item>
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
    </div>
</div>