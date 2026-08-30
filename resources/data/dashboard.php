<?php

/*
|--------------------------------------------------------------------------
| Mock dashboard data
|--------------------------------------------------------------------------
| Home screen primed data: greeting, activity feed and quick stats.
|
| TODO: Replace with real Activity / UsageRecord models once the AI layer is
| connected.
*/

return [
    'user' => [
        'firstName' => 'Darshan',
        'initials' => 'DP',
    ],
    'stats' => [
        ['label' => 'Executions', 'value' => 4823, 'format' => 'number', 'delta' => 18.4, 'interval' => 'vs last 30 days'],
        ['label' => 'Tokens', 'value' => 18642012, 'format' => 'compact', 'delta' => 12.1, 'interval' => 'vs last 30 days'],
        ['label' => 'Estimated cost', 'value' => 214.63, 'format' => 'currency', 'delta' => 9.7, 'interval' => 'vs last 30 days'],
        ['label' => 'Avg. latency', 'value' => 1240, 'format' => 'latency', 'delta' => -6.2, 'interval' => 'vs last 30 days'],
    ],
    'activity' => [
        ['type' => 'run', 'text' => 'ran "Code Review Assistant" on claude-3-7-sonnet', 'promptId' => 1, 'at' => '2026-08-29T18:42:00+00:00', 'meta' => '1.8s · 10,284 tokens'],
        ['type' => 'version', 'text' => 'created v7 of "Code Review Assistant"', 'promptId' => 1, 'at' => '2026-08-28T09:14:00+00:00', 'meta' => '+8 lines system prompt'],
        ['type' => 'favorite', 'text' => 'starred "Product Feature Blueprint"', 'promptId' => 8, 'at' => '2026-08-27T11:20:00+00:00', 'meta' => null],
        ['type' => 'run', 'text' => 'ran "Meeting Summary Scribe" on gpt-4o', 'promptId' => 6, 'at' => '2026-08-29T15:08:00+00:00', 'meta' => '0.9s · 3,850 tokens'],
        ['type' => 'project', 'text' => 'updated "Data Platform" project description', 'promptId' => null, 'at' => '2026-08-24T11:05:00+00:00', 'meta' => null],
        ['type' => 'run', 'text' => 'ran "SQL Explain Optimizer" on gpt-4o-mini', 'promptId' => 3, 'at' => '2026-08-29T09:55:00+00:00', 'meta' => 'failed · rate limit'],
        ['type' => 'version', 'text' => 'restored v4 of "SQL Explain Optimizer"', 'promptId' => 3, 'at' => '2026-08-22T14:40:00+00:00', 'meta' => null],
        ['type' => 'favorite', 'text' => 'starred "SQL Explain Optimizer"', 'promptId' => 3, 'at' => '2026-08-21T09:10:00+00:00', 'meta' => null],
        ['type' => 'run', 'text' => 'ran "SEO Meta Generator" on gemini-2.5-flash', 'promptId' => 4, 'at' => '2026-08-28T21:30:00+00:00', 'meta' => '0.8s · 944 tokens'],
        ['type' => 'project', 'text' => 'added "Research Ops" project', 'promptId' => null, 'at' => '2026-08-09T13:26:00+00:00', 'meta' => null],
    ],
];