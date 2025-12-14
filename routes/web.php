<?php

use App\Http\Controllers\LearnerProgressController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/learner-progress', [LearnerProgressController::class, 'index'])
    ->name('learner-progress.index');

// Seeding route for Railway deployments (only works in production)
Route::get('/seed-database', function () {
    if (!app()->environment('production')) {
        abort(404);
    }

    Artisan::call('db:seed', ['--force' => true]);

    return response()->json([
        'message' => 'Database seeded successfully!',
        'output' => Artisan::output()
    ]);
});
