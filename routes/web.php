<?php

use Illuminate\Support\Facades\Route;

// Route::view instead of a closure: closure-based routes CANNOT be cached,
// and `php artisan optimize` (run by the production entrypoint on boot)
// fails hard on them. Static view routes are cacheable and identical here.
Route::view('/', 'welcome');

Route::view('/home', 'home')->middleware('auth')->name('home');
