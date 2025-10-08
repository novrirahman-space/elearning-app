<?php

namespace App\Http\Controllers\Api;

use App\Events\ReplyCreated;
use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\Reply;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    // POST /api/discussions/{id}/replies
    public function store(Request $request, $id)
    {
        $user = $request->user();

        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $discussion = Discussion::with('course')->findOrFail($id);

        // Cek apakah user bagian dari course
        $course = $discussion->course;
        $isLecturer = $user->id === $course->lecturer_id;
        $isEnrolled = $user->coursesEnrolled()->where('course_id', $course->id)->exists();

        if (!$isLecturer && !$isEnrolled) {
            return response()->json([
                'message' => 'You are not part of this course'
            ], 403);
        }

        $reply = Reply::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => $validated['content']
        ]);

        // Trigger event broadcast
        event(new ReplyCreated($reply));

        return response()->json([
            'status' => 'success',
            'message' => 'Reply Added successfully.'
        ], 201);

    }

    //
}
