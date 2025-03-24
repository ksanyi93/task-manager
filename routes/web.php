<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});

// Először a specifikus /tasks/ prefixű útvonalakat helyezzük el
Route::get('/tasks/weekly', [TaskController::class, 'weekly'])->name('tasks.weekly');
Route::get('/tasks/weekly/{date}', [TaskController::class, 'weekly'])->name('tasks.weekly.date');

// Ezután az ID-alapú útvonalak következnek
Route::get('/tasks/{task}/reschedule', [TaskController::class, 'reschedule'])->name('tasks.reschedule');
Route::patch('/tasks/{task}/update-schedule', [TaskController::class, 'updateSchedule'])->name('tasks.update_schedule');
Route::get('/tasks/{task}/duplicate', [TaskController::class, 'duplicate'])->name('tasks.duplicate');

// Végül a resource útvonal, ami wildcard matchereket tartalmaz
Route::resource('tasks', TaskController::class);