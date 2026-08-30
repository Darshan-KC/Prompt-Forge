<?php

/*
|--------------------------------------------------------------------------
| Mock prompt versions
|--------------------------------------------------------------------------
| Version history per prompt. `current` marks the active version.
|
| TODO: Replace with real PromptVersion models created on save/restore.
*/

return [
    1 => [
        ['number' => 7, 'note' => 'Add severity weighting to review output', 'author' => 'Darshan Patel', 'createdAt' => '2026-08-28T09:14:00+00:00', 'current' => true],
        ['number' => 6, 'note' => 'Improved formatting', 'author' => 'Darshan Patel', 'createdAt' => '2026-08-20T15:02:00+00:00'],
        ['number' => 5, 'note' => 'Added diff size limits to system prompt', 'author' => 'Maya Chen', 'createdAt' => '2026-08-11T10:40:00+00:00'],
        ['number' => 4, 'note' => 'Original experiment', 'author' => 'Darshan Patel', 'createdAt' => '2026-08-05T09:30:00+00:00'],
    ],
    2 => [
        ['number' => 3, 'note' => 'Add benefit-first rule for headings', 'author' => 'Sofia Marques', 'createdAt' => '2026-08-26T16:40:00+00:00', 'current' => true],
        ['number' => 2, 'note' => 'Shorten sentences in output guidance', 'author' => 'Darshan Patel', 'createdAt' => '2026-07-18T11:20:00+00:00'],
        ['number' => 1, 'note' => 'Original experiment', 'author' => 'Darshan Patel', 'createdAt' => '2026-06-11T09:22:00+00:00'],
    ],
    3 => [
        ['number' => 5, 'note' => 'Added expected-impact ranking', 'author' => 'Lena Fischer', 'createdAt' => '2026-08-24T11:05:00+00:00', 'current' => true],
        ['number' => 4, 'note' => 'Restricted advice to low-risk changes', 'author' => 'Lena Fischer', 'createdAt' => '2026-08-12T14:10:00+00:00'],
        ['number' => 3, 'note' => 'Added constraints', 'author' => 'Darshan Patel', 'createdAt' => '2026-07-30T08:48:00+00:00'],
        ['number' => 2, 'note' => 'Improved EXPLAIN parsing hints', 'author' => 'Darshan Patel', 'createdAt' => '2026-06-02T17:05:00+00:00'],
        ['number' => 1, 'note' => 'Original experiment', 'author' => 'Lena Fischer', 'createdAt' => '2026-04-19T08:40:00+00:00'],
    ],
    6 => [
        ['number' => 6, 'note' => 'Add audience variable', 'author' => 'Darshan Patel', 'createdAt' => '2026-08-15T17:12:00+00:00', 'current' => true],
        ['number' => 5, 'note' => 'Restructure output into D/AI/Q sections', 'author' => 'Darshan Patel', 'createdAt' => '2026-08-06T12:00:00+00:00'],
        ['number' => 4, 'note' => 'Original experiment', 'author' => 'Maya Chen', 'createdAt' => '2026-03-14T09:00:00+00:00'],
    ],
    8 => [
        ['number' => 3, 'note' => 'Add open-question challenge step', 'author' => 'Darshan Patel', 'createdAt' => '2026-08-09T13:26:00+00:00', 'current' => true],
        ['number' => 2, 'note' => 'Constrain scope to one-pager', 'author' => 'Sofia Marques', 'createdAt' => '2026-07-29T16:20:00+00:00'],
        ['number' => 1, 'note' => 'Original experiment', 'author' => 'Darshan Patel', 'createdAt' => '2026-07-22T15:36:00+00:00'],
    ],
    5 => [
        ['number' => 4, 'note' => 'Added boundary-value guidance', 'author' => 'Lena Fischer', 'createdAt' => '2026-08-18T08:30:00+00:00', 'current' => true],
        ['number' => 3, 'note' => 'Switch to Given/When/Then', 'author' => 'Lena Fischer', 'createdAt' => '2026-08-02T13:22:00+00:00'],
        ['number' => 2, 'note' => 'Improved exception coverage', 'author' => 'Darshan Patel', 'createdAt' => '2026-07-10T10:05:00+00:00'],
        ['number' => 1, 'note' => 'Original experiment', 'author' => 'Darshan Patel', 'createdAt' => '2026-05-30T12:45:00+00:00'],
    ],
    10 => [
        ['number' => 4, 'note' => 'Add example request section', 'author' => 'Maya Chen', 'createdAt' => '2026-08-02T15:18:00+00:00', 'current' => true],
        ['number' => 3, 'note' => 'Add error table requirement', 'author' => 'Maya Chen', 'createdAt' => '2026-07-15T09:12:00+00:00'],
        ['number' => 2, 'note' => 'Improved skimmability rules', 'author' => 'Darshan Patel', 'createdAt' => '2026-07-01T11:45:00+00:00'],
        ['number' => 1, 'note' => 'Original experiment', 'author' => 'Darshan Patel', 'createdAt' => '2026-06-20T11:11:00+00:00'],
    ],
    4 => [
        ['number' => 2, 'note' => 'Add keyword + points variables', 'author' => 'Sofia Marques', 'createdAt' => '2026-08-20T14:22:00+00:00', 'current' => true],
        ['number' => 1, 'note' => 'Original experiment', 'author' => 'Sofia Marques', 'createdAt' => '2026-07-01T10:00:00+00:00'],
    ],
    7 => [
        ['number' => 1, 'note' => 'Original experiment', 'author' => 'Darshan Patel', 'createdAt' => '2026-08-12T10:40:00+00:00', 'current' => true],
    ],
    9 => [
        ['number' => 2, 'note' => 'Flag leading questions', 'author' => 'Darshan Patel', 'createdAt' => '2026-08-05T09:50:00+00:00', 'current' => true],
        ['number' => 1, 'note' => 'Original experiment', 'author' => 'Darshan Patel', 'createdAt' => '2026-07-28T18:05:00+00:00'],
    ],
];