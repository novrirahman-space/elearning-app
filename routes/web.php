<?php

use Illuminate\Support\Facades\Route;
use App\Models\Course;
use App\Models\Discussion;

Route::get('/forum/{course}', function (Course $course) {
    $discussions = $course->discussions()->with('user', 'replies.user')->latest()->get();

    return view('forum', compact('course', 'discussions'));
}); //tambahkan middleware untuk kedepannya

Route::get('/', function () {
    return view('welcome');
});
