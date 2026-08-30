<?php

/*
|--------------------------------------------------------------------------
| Mock projects
|--------------------------------------------------------------------------
| Project organization data (frontend-only currently; no collaboration backend).
|
| TODO: Replace with real Project Eloquent models / ProjectRepository.
*/

return [
    [
        'id' => 1,
        'slug' => 'prompt-forge-product',
        'name' => 'Prompt-Forge Product',
        'description' => 'Everything that ships with the Prompt-Forge itself: prompts, docs, support scripts and launch copy.',
        'color' => '#ec5d12',
        'promptCount' => 6,
        'runCount' => 1410,
        'lastActivity' => '2026-08-28T09:14:00+00:00',
        'recentPromptIds' => [1, 2, 8, 10],
        'members' => [
            ['name' => 'Darshan Patel', 'initials' => 'DP', 'role' => 'Owner'],
            ['name' => 'Maya Chen', 'initials' => 'MC', 'role' => 'Engineer'],
            ['name' => 'Ryan Alvarez', 'initials' => 'RA', 'role' => 'Designer'],
        ],
    ],
    [
        'id' => 2,
        'slug' => 'data-platform',
        'name' => 'Data Platform',
        'description' => 'Users, billing and analytics platform for Prompt-Forge customers.',
        'color' => '#3b82f6',
        'promptCount' => 3,
        'runCount' => 872,
        'lastActivity' => '2026-08-24T11:05:00+00:00',
        'recentPromptIds' => [3, 5, 6],
        'members' => [
            ['name' => 'Darshan Patel', 'initials' => 'DP', 'role' => 'Owner'],
            ['name' => 'Lena Fischer', 'initials' => 'LF', 'role' => 'Engineer'],
        ],
    ],
    [
        'id' => 3,
        'slug' => 'marketing-site',
        'name' => 'Marketing Site',
        'description' => 'Public site copy, SEO meta and campaign experiments.',
        'color' => '#8b5cf6',
        'promptCount' => 4,
        'runCount' => 431,
        'lastActivity' => '2026-08-20T14:22:00+00:00',
        'recentPromptIds' => [4],
        'members' => [
            ['name' => 'Sofia Marques', 'initials' => 'SM', 'role' => 'Marketer'],
        ],
    ],
    [
        'id' => 4,
        'slug' => 'research-ops',
        'name' => 'Research Ops',
        'description' => 'User interviews and analysis workflows.',
        'color' => '#10b981',
        'promptCount' => 2,
        'runCount' => 118,
        'lastActivity' => '2026-08-09T13:26:00+00:00',
        'recentPromptIds' => [8, 9],
        'members' => [
            ['name' => 'Darshan Patel', 'initials' => 'DP', 'role' => 'Owner'],
        ],
    ],
];