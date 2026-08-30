<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Central access point for the static frontend data set.
 *
 * Every method reads from a single file in resources/data/ so the whole mock
 * layer can be swapped for real repositories later without touching views.
 *
 * TODO: Replace these methods with real backend accessors (Eloquent models /
 * repositories) as the AI/backend layer is implemented.
 */
class MockData
{
    /** @var array<string, array> */
    protected static array $cache = [];

    public static function load(string $file): array
    {
        if (! isset(static::$cache[$file])) {
            /** @var array */
            static::$cache[$file] = require resource_path("data/{$file}.php");
        }

        return static::$cache[$file];
    }

    public static function providers(): array
    {
        return static::load('providers');
    }

    public static function provider(string $slug): ?array
    {
        foreach (static::providers() as $provider) {
            if ($provider['slug'] === $slug) {
                return $provider;
            }
        }

        return null;
    }

    public static function models(): Collection
    {
        return collect(static::providers())
            ->flatMap(fn (array $provider) => collect($provider['models'])
                ->map(fn (array $model) => $model + ['provider' => $provider['slug'], 'providerName' => $provider['name']]))
            ->values();
    }

    public static function model(string $slug, ?string $providerSlug = null): ?array
    {
        return static::models()
            ->first(fn (array $model) => $model['slug'] === $slug
                && ($providerSlug === null || $model['provider'] === $providerSlug));
    }

    public static function prompts(): array
    {
        return static::load('prompts');
    }

    public static function prompt(int $id): ?array
    {
        foreach (static::prompts() as $prompt) {
            if ($prompt['id'] === $id) {
                return $prompt;
            }
        }

        return null;
    }

    public static function folders(): array
    {
        return [
            ['id' => 1, 'name' => 'Engineering', 'color' => '#ec5d12'],
            ['id' => 2, 'name' => 'Content', 'color' => '#3b82f6'],
            ['id' => 3, 'name' => 'Product & Marketing', 'color' => '#8b5cf6'],
        ];
    }

    public static function folder(int $id): ?array
    {
        foreach (static::folders() as $folder) {
            if ($folder['id'] === $id) {
                return $folder;
            }
        }

        return null;
    }

    public static function categories(): array
    {
        return ['Development', 'Writing', 'Research', 'Marketing', 'Education', 'Data Analysis', 'Productivity'];
    }

    public static function projects(): array
    {
        return static::load('projects');
    }

    public static function project(int $id): ?array
    {
        foreach (static::projects() as $project) {
            if ($project['id'] === $id) {
                return $project;
            }
        }

        return null;
    }

    public static function runs(): array
    {
        return static::load('prompt-runs');
    }

    public static function run(int $id): ?array
    {
        foreach (static::runs() as $run) {
            if ($run['id'] === $id) {
                return $run;
            }
        }

        return null;
    }

    public static function versions(): array
    {
        return static::load('versions');
    }

    public static function versionsFor(int $promptId): array
    {
        return static::versions()[$promptId] ?? [];
    }

    public static function analytics(): array
    {
        return static::load('analytics');
    }

    public static function dashboard(): array
    {
        return static::load('dashboard');
    }

    public static function activity(): array
    {
        return static::dashboard()['activity'];
    }

    public static function recentPrompts(int $limit = 4): Collection
    {
        $prompts = collect(static::prompts())
            ->sortByDesc('updatedAt')
            ->take($limit)
            ->values();

        return $prompts;
    }

    public static function recentRuns(int $limit = 5): Collection
    {
        return collect(static::runs())->sortByDesc('createdAt')->take($limit)->values();
    }

    /**
     * Render an estimated cost as a currency string.
     */
    public static function money(float $value): string
    {
        return '$'.number_format($value, $value < 1 ? 4 : 2);
    }

    /**
     * Render a token count compactly (12,482,012 -> 12.4M).
     */
    public static function compactNumber(int $value): string
    {
        if ($value >= 1_000_000) {
            return number_format($value / 1_000_000, $value >= 10_000_000 ? 0 : 1).'M';
        }

        if ($value >= 1_000) {
            return number_format($value / 1_000, 0).'K';
        }

        return number_format($value);
    }

    /**
     * Render a latency value (milliseconds) human-friendly.
     */
    public static function latency(int $ms): string
    {
        if ($ms < 1000) {
            return $ms.'ms';
        }

        return number_format($ms / 1000, 1).'s';
    }

    public static function timeAgo(string $iso): string
    {
        $diff = now()->diffInMinutes($iso);

        return match (true) {
            $diff < 1 => 'just now',
            $diff < 60 => $diff.'m ago',
            $diff < 1440 => intdiv($diff, 60).'h ago',
            default => now()->parse($iso)->format('M j, Y'),
        };
    }

    public static function statusConfig(string $status): array
    {
        return match ($status) {
            'success' => ['label' => 'Success', 'classes' => 'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300'],
            'error' => ['label' => 'Failed', 'classes' => 'bg-red-500/10 text-red-700 dark:bg-red-400/10 dark:text-red-300'],
            'cancelled' => ['label' => 'Stopped', 'classes' => 'bg-zinc-500/10 text-zinc-600 dark:bg-zinc-400/10 dark:text-zinc-300'],
            'rate_limited' => ['label' => 'Rate limited', 'classes' => 'bg-amber-500/10 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300'],
            'running' => ['label' => 'Running', 'classes' => 'bg-brand-500/10 text-brand-700 dark:bg-brand-400/10 dark:text-brand-300'],
            'draft' => ['label' => 'Draft', 'classes' => 'bg-zinc-500/10 text-zinc-600 dark:bg-zinc-400/10 dark:text-zinc-300'],
            'published' => ['label' => 'Published', 'classes' => 'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300'],
            'connected' => ['label' => 'Connected', 'classes' => 'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300'],
            'configured' => ['label' => 'Configured', 'classes' => 'bg-sky-500/10 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300'],
            'disconnected' => ['label' => 'Disconnected', 'classes' => 'bg-zinc-500/10 text-zinc-600 dark:bg-zinc-400/10 dark:text-zinc-300'],
            default => ['label' => ucfirst($status), 'classes' => 'bg-zinc-500/10 text-zinc-600 dark:bg-zinc-400/10 dark:text-zinc-300'],
        };
    }

    public static function providerColor(string $slug): string
    {
        return static::provider($slug)['color'] ?? '#71717a';
    }
}