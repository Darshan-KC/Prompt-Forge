@php
    $existing = \App\Support\MockData::projects();
    $colors = ['#ec5d12', '#3b82f6', '#8b5cf6', '#10b981', '#ef4444'];
@endphp

<x-app.page-container class="space-y-6">
    <x-shared.page-header eyebrow="Projects" title="New project" description="Group related prompts into a shared workspace.">
        <x-slot:actions>
            <flux:link href="{{ route('projects.index') }}" wire:navigate variant="subtle">Cancel</flux:link>
        </x-slot:actions>
    </x-shared.page-header>

    <form x-data="{ name: '', description: '', color: {{ Js::from($colors[0]) }}, colors: {{ Js::from($colors) }} }"
        x-on:submit.prevent="if (!name.trim()) return; $dispatch('pf-toast', { icon: 'check-circle', title: 'Project created', message: '“' + name + '” was created (mock).' })"
        class="max-w-2xl space-y-6">

        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-xs sm:p-6 dark:border-white/10 dark:bg-zinc-900/60">
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input x-model="name" placeholder="e.g. Data Platform" required />
                </flux:field>

                <flux:field>
                    <flux:label>Description</flux:label>
                    <flux:textarea x-model="description" rows="3" placeholder="What is this project about?" />
                </flux:field>

                <flux:field>
                    <flux:label>Accent color</flux:label>
                    <div class="mt-2 flex items-center gap-2.5">
                        <template x-for="c in colors" :key="c">
                            <button type="button" x-on:click="color = c"
                                class="size-8 rounded-full border-4 transition"
                                :class="color === c ? 'border-zinc-900 dark:border-white' : 'border-transparent'"
                                :style="'background-color:' + c"
                                :aria-label="'use ' + c" :aria-pressed="color === c"></button>
                        </template>
                        <span class="ml-auto inline-flex items-center gap-2 px-2.5 text-xs text-zinc-400">
                            <flux:icon.swatch class="size-3.5" />
                            Preview
                        </span>
                    </div>
                    <div class="mt-3 inline-flex items-center gap-2.5 rounded-lg border border-zinc-200 px-3 py-2 dark:border-white/10">
                        <span class="grid size-6 place-items-center rounded text-[0.7rem] font-bold text-white" :style="'background-color:' + color">PF</span>
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-200" x-text="name || 'Untitled project'"></span>
                    </div>
                </flux:field>
            </div>
        </section>

        @if (count($existing))
            <p class="text-xs text-zinc-400 dark:text-zinc-500">
                Note: this is a static mock — new projects appear in the UI after the backend is wired to storage.
            </p>
        @endif

        <div class="flex items-center gap-2">
            <flux:button variant="primary" type="submit" icon="check">Create project</flux:button>
            <flux:link href="{{ route('projects.index') }}" wire:navigate variant="subtle">Cancel</flux:link>
        </div>
    </form>
</x-app.page-container>