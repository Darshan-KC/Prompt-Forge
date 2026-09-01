<x-layouts.app>
@php
    $projects = \App\Support\MockData::projects();
    $totalRuns = collect($projects)->sum('runCount');
@endphp

<x-app.page-container class="space-y-6">
    <x-shared.page-header
        eyebrow="Workspace"
        title="Projects"
        description="Group prompts around products, teams and experiments — {{ count($projects) }} projects, {{ number_format($totalRuns) }} total runs."
    >
        <x-slot:actions>
            <flux:button href="{{ route('projects.create') }}" wire:navigate variant="primary" icon="plus">New project</flux:button>
        </x-slot:actions>
    </x-shared.page-header>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($projects as $project)
            <a href="{{ route('projects.show', $project['id']) }}" wire:navigate
                class="group relative flex flex-col rounded-xl border border-zinc-200 bg-white p-5 shadow-xs transition duration-150 hover:border-zinc-300 hover:shadow-md dark:border-white/10 dark:bg-zinc-900/60 dark:hover:border-white/15">

                <div class="flex items-center gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-lg font-semibold text-white"
                        style="background-color: {{ $project['color'] }}">
                        {{ collect(explode(' ', $project['name']))->map(fn ($w) => $w[0])->take(2)->implode('') }}
                    </span>
                    <div class="min-w-0">
                        <h3 class="truncate text-sm font-semibold text-zinc-900 transition group-hover:text-brand-700 dark:text-zinc-100 dark:group-hover:text-brand-300">
                            {{ $project['name'] }}
                        </h3>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $project['promptCount'] }} prompts · {{ number_format($project['runCount']) }} runs</p>
                    </div>
                    <flux:icon.chevron-right class="ml-auto size-4 shrink-0 text-zinc-300 transition group-hover:translate-x-0.5 group-hover:text-brand-500 dark:text-zinc-600" />
                </div>

                <p class="mt-3 line-clamp-2 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $project['description'] }}</p>

                <div class="mt-4 flex items-center justify-between gap-3 border-t border-zinc-100 pt-3.5 dark:border-white/5">
                    <div class="flex -space-x-1.5">
                        @foreach ($project['members'] as $member)
                            <x-avatar :name="$member['name']" class="size-6 !text-[0.6rem]" :title="$member['name']" />
                        @endforeach
                        @if (count($project['members']) > 1)
                            <span class="z-10 inline-flex size-6 items-center justify-center rounded-full border-2 border-white bg-zinc-100 text-[0.6rem] font-semibold text-zinc-500 dark:border-zinc-900 dark:bg-zinc-800 dark:text-zinc-300"></span>
                        @endif
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs text-zinc-400 dark:text-zinc-500">
                        <flux:icon.clock class="size-3.5" />
                        {{ \App\Support\MockData::timeAgo($project['lastActivity']) }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</x-app.page-container>
</x-layouts.app>
