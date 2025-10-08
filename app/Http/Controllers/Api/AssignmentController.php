<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class AssignmentController extends Controller
{
    //POST /api/assignments
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isLecturer()) {
            return response()->json([
                'message' => 'Only lecturers can create assignments.'
            ], 403);
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date'
        ]);

        // Cek apakah course milik dosen
        $course = Course::findOrFail($validated['course_id']);
        if ($course->lecturer_id !== $user->id) {
            return response()->json([
                'message' => 'You can only add assignments to your own courses.',
            ]);
        }

        $assignment = Assignment::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Assignment created successfully.',
            'data' => $assignment
        ], 201);
    }

    //
}
