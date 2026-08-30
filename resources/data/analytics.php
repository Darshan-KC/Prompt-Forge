<?php

/*
|--------------------------------------------------------------------------
| Mock analytics
|--------------------------------------------------------------------------
| Static analytics for the Analytics page. Charts are hand-curated so the
| whole experience renders without a backend aggregator.
|
| TODO: Replace with real usage aggregations (PromptRun model + query builder)
| computed by the backend analytics layer.
*/

return [
    'period' => [
        'label' => 'Last 30 days',
        'from' => '2026-08-01',
        'to' => '2026-08-29',
        'previousLabel' => 'Previous 30 days',
    ],
    'totals' => [
        'executions' => 4823,
        'executionDelta' => 18.4,
        'tokens' => 18642012,
        'tokenDelta' => 12.1,
        'cost' => 214.63,
        'costDelta' => 9.7,
        'avgLatency' => 1240,
        'latencyDelta' => -6.2,
        'inputTokens' => 12483012,
        'outputTokens' => 6159000,
        'successRate' => 97.2,
        'successDelta' => 0.9,
    ],
    'usageOverTime' => [
        ['label' => 'Aug 01', 'executions' => 128, 'tokens' => 420000, 'cost' => 5.1],
        ['label' => 'Aug 03', 'executions' => 151, 'tokens' => 510000, 'cost' => 6.0],
        ['label' => 'Aug 05', 'executions' => 138, 'tokens' => 448000, 'cost' => 5.4],
        ['label' => 'Aug 07', 'executions' => 164, 'tokens' => 592000, 'cost' => 6.8],
        ['label' => 'Aug 09', 'executions' => 149, 'tokens' => 505000, 'cost' => 5.9],
        ['label' => 'Aug 11', 'executions' => 172, 'tokens' => 610000, 'cost' => 7.2],
        ['label' => 'Aug 13', 'executions' => 158, 'tokens' => 540000, 'cost' => 6.3],
        ['label' => 'Aug 15', 'executions' => 181, 'tokens' => 668000, 'cost' => 7.9],
        ['label' => 'Aug 17', 'executions' => 166, 'tokens' => 601000, 'cost' => 7.1],
        ['label' => 'Aug 19', 'executions' => 190, 'tokens' => 712000, 'cost' => 8.3],
        ['label' => 'Aug 21', 'executions' => 176, 'tokens' => 634000, 'cost' => 7.5],
        ['label' => 'Aug 23', 'executions' => 198, 'tokens' => 745000, 'cost' => 8.7],
        ['label' => 'Aug 25', 'executions' => 184, 'tokens' => 702000, 'cost' => 8.2],
        ['label' => 'Aug 27', 'executions' => 205, 'tokens' => 788000, 'cost' => 9.2],
        ['label' => 'Aug 29', 'executions' => 218, 'tokens' => 821000, 'cost' => 9.6],
    ],
    'modelUsage' => [
        ['model' => 'claude-3-7-sonnet', 'provider' => 'anthropic', 'executions' => 1830, 'tokens' => 7120000, 'cost' => 96.4, 'share' => 38.0],
        ['model' => 'gpt-4o', 'provider' => 'openai', 'executions' => 1412, 'tokens' => 5340000, 'cost' => 61.2, 'share' => 29.3],
        ['model' => 'gpt-4o-mini', 'provider' => 'openai', 'executions' => 840, 'tokens' => 2440000, 'cost' => 12.8, 'share' => 17.4],
        ['model' => 'gemini-2.5-flash', 'provider' => 'google', 'executions' => 421, 'tokens' => 1830000, 'cost' => 14.0, 'share' => 8.7],
        ['model' => 'claude-3-5-haiku', 'provider' => 'anthropic', 'executions' => 320, 'tokens' => 1905412, 'cost' => 30.2, 'share' => 6.6],
    ],
    'providerUsage' => [
        ['provider' => 'anthropic', 'executions' => 2150, 'tokens' => 9025412, 'cost' => 126.6, 'share' => 44.6],
        ['provider' => 'openai', 'executions' => 2252, 'tokens' => 7780000, 'cost' => 74.0, 'share' => 46.7],
        ['provider' => 'google', 'executions' => 421, 'tokens' => 1830000, 'cost' => 14.0, 'share' => 8.7],
    ],
    'topPrompts' => [
        ['promptId' => 1, 'name' => 'Code Review Assistant', 'executions' => 1284, 'tokens' => 12282000, 'cost' => 61.1],
        ['promptId' => 6, 'name' => 'Meeting Summary Scribe', 'executions' => 896, 'tokens' => 7840000, 'cost' => 39.8],
        ['promptId' => 5, 'name' => 'Test Case Generator', 'executions' => 710, 'tokens' => 1980000, 'cost' => 18.1],
        ['promptId' => 3, 'name' => 'SQL Explain Optimizer', 'executions' => 510, 'tokens' => 1560000, 'cost' => 11.4],
        ['promptId' => 2, 'name' => 'Release Notes Writer', 'executions' => 342, 'tokens' => 830000, 'cost' => 6.9],
    ],
    'expensivePrompts' => [
        ['promptId' => 1, 'name' => 'Code Review Assistant', 'avgCost' => 0.0476, 'avgLatency' => 1480, 'runs' => 1284],
        ['promptId' => 7, 'name' => 'Rubric Grader', 'avgCost' => 0.0183, 'avgLatency' => 2040, 'runs' => 118],
        ['promptId' => 3, 'name' => 'SQL Explain Optimizer', 'avgCost' => 0.0224, 'avgLatency' => 1060, 'runs' => 510],
        ['promptId' => 10, 'name' => 'API Doc Rewriter', 'avgCost' => 0.0202, 'avgLatency' => 1380, 'runs' => 287],
        ['promptId' => 6, 'name' => 'Meeting Summary Scribe', 'avgCost' => 0.0444, 'avgLatency' => 1200, 'runs' => 896],
    ],
];