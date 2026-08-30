<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Application routes use Route::view (or controller-based) instead of
| closures: closure-based routes CANNOT be cached, and `php artisan optimize`
| (run by the production entrypoint on boot) fails hard on them. Static view
| routes are cacheable and identical here.
*/

// Guest landing page.
Route::view('/', 'welcome')->name('welcome');

// Authenticated workspace screens. All static pages source their data from
// App\Support\MockData so they can be swapped for real endpoints later.
Route::middleware('auth')->group(function () {
    Route::view('/home', 'screens.dashboard')->name('home');
    Route::view('/dashboard', 'screens.dashboard')->name('dashboard');

    Route::view('/playground', 'screens.playground')->name('playground');
    Route::view('/playground/{prompt?}', 'screens.playground')->name('playground.prompt');

    // Prompt library.
    Route::view('/prompts', 'screens.prompts.index')->name('prompts.index');
    Route::view('/prompts/create', 'screens.prompts.create')->name('prompts.create');
    Route::view('/prompts/{prompt}/versions', 'screens.prompts.versions')->name('prompts.versions');
    Route::view('/prompts/{prompt}/compare', 'screens.prompts.compare')->name('prompts.compare');
    Route::view('/prompts/{prompt}', 'screens.prompts.show')->name('prompts.show');

    // Projects.
    Route::view('/projects', 'screens.projects.index')->name('projects.index');
    Route::view('/projects/create', 'screens.projects.create')->name('projects.create');
    Route::view('/projects/{project}', 'screens.projects.show')->name('projects.show');

    // Run history.
    Route::view('/history', 'screens.history.index')->name('history.index');
    Route::view('/history/{run}', 'screens.history.show')->name('history.show');

    Route::view('/analytics', 'screens.analytics')->name('analytics');

    // Settings tabs.
    Route::view('/settings/profile', 'screens.settings')->defaults('section', 'profile')->name('settings.profile');
    Route::view('/settings/appearance', 'screens.settings')->defaults('section', 'appearance')->name('settings.appearance');
    Route::view('/settings/providers', 'screens.settings')->defaults('section', 'providers')->name('settings.providers');
    Route::view('/settings/models', 'screens.settings')->defaults('section', 'models')->name('settings.models');
    Route::view('/settings/api-keys', 'screens.settings')->defaults('section', 'api-keys')->name('settings.api-keys');
    Route::view('/settings/notifications', 'screens.settings')->defaults('section', 'notifications')->name('settings.notifications');
    Route::view('/settings/account', 'screens.settings')->defaults('section', 'account')->name('settings.account');
});
