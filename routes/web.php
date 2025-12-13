<?php

use App\Http\Controllers\LearnerProgressController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/learner-progress', [LearnerProgressController::class, 'index'])
    ->name('learner-progress.index');
