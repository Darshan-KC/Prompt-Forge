<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UiSmokeTest extends TestCase
{
    public function test_public_pages_render(): void
    {
        foreach (['/', '/login', '/register', '/forgot-password'] as $url) {
            $this->get($url)->assertStatus(200);
        }
    }

    public function test_authenticated_screens_render(): void
    {
        $user = new User(['name' => 'Darshan', 'email' => 'darshan@promptforge.test']);

        $urls = [
            '/home',
            '/dashboard',
            '/playground',
            '/playground/prompt-1',
            '/prompts',
            '/prompts/create',
            '/prompts/prompt-1',
            '/prompts/prompt-1/versions',
            '/prompts/prompt-1/compare',
            '/projects',
            '/projects/create',
            '/projects/project-1',
            '/history',
            '/history/run-1',
            '/analytics',
            '/settings/profile',
            '/settings/appearance',
            '/settings/providers',
            '/settings/models',
            '/settings/api-keys',
            '/settings/notifications',
            '/settings/account',
        ];

        foreach ($urls as $url) {
            $this->actingAs($user)->get($url)->assertStatus(200);
        }
    }
}