@php
    $projectId = is_numeric($project ?? null) ? (int) $project : null;
    $projectData = $projectId !== null && $projectId > 0 ? \App\Support\MockData::project($projectId) : null;
    $prompts = $projectData
        ? collect(\App\Support\MockData::prompts())->filter(fn ($p) => $p['projectId'] === $projectId)->values()->all()
        : [];
    $runs = \App\Support\MockData::runs();
    $lastActivityLabel = $projectData && $projectData['lastActivity'] ? \App\Support\MockData::timeAgo($projectData['lastActivity']) : 'n/a';
    $projectPromptIds = collect($prompts)->pluck('id')->all();
    $projectRuns = collect($runs)->filter(fn ($r) => in_array($r['promptId'], $projectPromptIds))->take(6);
@endphp

<x-app.page-container class="space-y-6">
    @if (! $projectData)
        <div class="mx-auto max-w-sm py-20">
            <x-shared.empty-state icon="folder" title="Project not found" description="This project doesn't exist in the mock workspace.">
                <flux:button href="{{ route('projects.index') }}" wire:navigate variant="primary" size="sm">Back to projects</flux:button>
            </x-shared.empty-state>
        </div>
    @else
        <x-shared.page-header
            eyebrow="Projects"
            :title="$projectData['name']"
            :description="$projectData['description']"
        >
            <x-slot:actions>
                <flux:button variant="primary" icon="plus" href="{{ route('prompts.create') }}" wire:navigate>Add prompt</flux:button>
                <flux:link href="{{ route('projects.index') }}" wire:navigate variant="subtle">All projects</flux:link>
            </x-slot:actions>
        </x-shared.page-header>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <div class="min-w-0 space-y-6">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <x-shared.metric-card label="Prompts" :value="$projectData['promptCount']" icon="pencil-square" :interval="'across this project'" />
                    <x-shared.metric-card label="Runs" :value="number_format($projectData['runCount'])" icon="bolt" :interval="'all time'" />
                    <x-shared.metric-card label="Members" :value="count($projectData['members'])" icon="users" :interval="'in this project'" />
                    <x-shared.metric-card label="Activity" value="—" icon="clock" :interval="$lastActivityLabel" />
                </div>

                @if (count($prompts))
                    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                        <header class="flex items-center justify-between px-4 py-3 dark:border-white/5">
                            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Prompts in this project</h2>
                            <flux:link href="{{ route('prompts.index') }}" wire:navigate variant="subtle" size="sm" icon:trailing="arrow-right">Library</flux:link>
                        </header>
                        <div class="border-t border-zinc-100 dark:border-white/5">
                            @foreach ($prompts as $prompt)
                                <x-prompt.prompt-row :prompt="$prompt" />
                            @endforeach
                        </div>
                    </section>
                @else
                    <x-shared.empty-state icon="pencil-square" title="No prompts yet" description="Add your first prompt to this project to get started.">
                        <flux:button href="{{ route('prompts.create') }}" wire:navigate variant="primary" size="sm" icon="plus">New prompt</flux:button>
                    </x-shared.empty-state>
                @endif

                <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                    <header class="flex items-center justify-between px-4 py-3 dark:border-white/5">
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Recent activity</h2>
                        <flux:link href="{{ route('history.index') }}" wire:navigate variant="subtle" size="sm" icon:trailing="arrow-right">History</flux:link>
                    </header>
                    <div class="border-t border-zinc-100 dark:border-white/5">
                        @if ($projectRuns->isNotEmpty())
                            @foreach ($projectRuns as $run)
                                <a href="{{ route('history.show', $run['id']) }}" wire:navigate class="group flex items-center gap-3 border-b border-zinc-100 px-4 py-3 transition last:border-0 hover:bg-zinc-800/[0.02] dark:border-white/5 dark:hover:bg-white/[0.02]">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $run['promptName'] }}</p>
                                        <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">{{ \App\Support\MockData::timeAgo($run['createdAt']) }} · {{ number_format($run['tokens']) }} tokens</p>
                                    </div>
                                    <x-shared.status-badge :status="$run['status']" :dot="false" />
                                </a>
                            @endforeach
                        @else
                            <p class="px-4 py-6 text-center text-xs text-zinc-400 dark:text-zinc-500">No recent runs for this project.</p>
                        @endif
                    </div>
                </section>
            </div>

            <aside class="min-w-0 space-y-6">
                <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                    <header class="border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Members</h2>
                    </header>
                    <ul class="divide-y divide-zinc-100 px-4 dark:divide-white/5">
                        @foreach ($projectData['members'] as $member)
                            <li class="flex items-center gap-3 py-3">
                                <x-avatar :name="$member['name']" class="size-8 !text-[0.65rem]" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $member['name'] }}</p>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $member['role'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </aside>
        </div>
    @endif
</x-app.page-container>