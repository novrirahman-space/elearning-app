<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Discussion;
use App\Events\DiscussionCreated;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    // POST /api/discussions
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'content' => 'required|string'
        ]);

        $course = Course::findOrFail($validated['course_id']);

        // Cek apakah user dosen/mahasiswa terdaftar di course
        $isLecturer = $user->id === $course->lecturer_id;
        $isEnrolled = $user->coursesEnrolled()->where('course_id', $course->id)->exists();

        if (!$isLecturer && !$isEnrolled) {
            return response()->json([
                'message' => 'You are not part of this course'
            ], 403);
        }

        $discussion = Discussion::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'content' => $validated['content']
        ]);

        // Trigger event broadcast
        event(new DiscussionCreated($discussion));

        return response()->json([
            'status' => 'success',
            'message' => 'Discussion created successfully',
            "data" => $discussion
        , 201]);
    }

    //
}
