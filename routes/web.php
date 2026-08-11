<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;

Route::get('/', function () {
    return redirect()->route('courses.showAll');
});

Route::get('/courses/all', [CourseController::class, 'showAllCourses'])->name('courses.showAll');
Route::resource('courses', CourseController::class)->only(['create','store','destroy']);


