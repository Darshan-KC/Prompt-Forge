<div class="space-y-6" x-data="{ theme: (() => { const t = window.pfTheme?.current?.() || 'system'; return t === 'dark' || t === 'light' ? t : 'system'; })(), setTheme(t) { window.pfTheme.apply(t); this.theme = t; } }">
    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
        <header class="border-b border-zinc-100 px-5 py-4 dark:border-white/5">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Theme</h2>
            <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">Applied instantly and remembered on this device.</p>
        </header>
        <div class="grid gap-4 p-5 sm:grid-cols-3 sm:p-6">
            @foreach (['light' => ['Light', 'sun', 'Clean, bright surfaces with dark text.'], 'dark' => ['Dark', 'moon', 'Steel & ember — designed for dark first.'], 'system' => ['System', 'computer-desktop', 'Follow your operating system.']] as $key => [$label, $icon, $desc])
                <button type="button" x-on:click="setTheme('{{ $key }}')"
                    class="group rounded-xl border p-4 text-left transition"
                    :class="theme === '{{ $key }}' ? 'border-brand-500/60 bg-brand-500/5 ring-1 ring-brand-500/20' : 'border-zinc-200 hover:border-zinc-300 dark:border-white/10 dark:hover:border-white/15'">
                    <div class="flex items-center justify-between">
                        <span class="grid size-8 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                            <flux:icon :icon="$icon" class="size-4" />
                        </span>
                        <flux:icon.check-circle class="size-4 text-brand-600" x-show="theme === '{{ $key }}'" />
                    </div>
                    <p class="mt-3 text-sm font-semibold text-zinc-900 dark:text-white">{{ $label }}</p>
                    <p class="mt-1 text-xs leading-relaxed text-zinc-400 dark:text-zinc-500">{{ $desc }}</p>
                </button>
            @endforeach
        </div>
    </section>

    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
        <header class="border-b border-zinc-100 px-5 py-4 dark:border-white/5">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Accent</h2>
            <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">Brand color is curried across buttons, links and highlights.</p>
        </header>
        <div class="flex flex-wrap items-center gap-3 p-5 sm:p-6">
            @foreach (['#fb7523', '#0d9488', '#4f46e5', '#db2777'] as $accent)
                <button type="button" x-data x-on:click="$dispatch('pf-toast', { title: 'Preview only', message: 'Custom accents ship with the theming backend.' })"
                    class="size-9 rounded-full border-4 border-white shadow ring-1 ring-zinc-200 dark:border-zinc-900 dark:ring-white/10"
                    style="background-color: {{ $accent }}"
                    aria-label="Preview accent {{ $accent }}"></button>
            @endforeach
            <span class="inline-flex items-center gap-1.5 text-xs text-zinc-400">
                <flux:icon.lock-closed class="size-3.5" />
                Custom accents coming with theming backend
            </span>
        </div>
    </section>

    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
        <header class="border-b border-zinc-100 px-5 py-4 dark:border-white/5">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Interface</h2>
        </header>
        <div class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6">
            <flux:field>
                <flux:label>Language</flux:label>
                <flux:select>
                    <flux:select.option value="en">English</flux:select.option>
                </flux:select>
            </flux:field>
            <flux:field>
                <flux:label>Editor font</flux:label>
                <flux:select>
                    <flux:select.option value="mono">JetBrains Mono</flux:select.option>
                    <flux:select.option value="ui">System UI</flux:select.option>
                </flux:select>
            </flux:field>
        </div>
    </section>
</div>