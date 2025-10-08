<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Course;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Broadcast::channel('course.{courseId}', function ($user, $courseId) {
    $course = Course::find($courseId);
    if (!$course)
        return false;

    return $user->id === $course->lecturer_id || $user->coursesEnrolled()->where('course_id', $courseId)->exists();
});
