<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Mail\NewAssignmentNotification;
use Illuminate\Support\Facades\Mail;
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

        // Ambil course beserta mahasiswa yang terdaftar
        $course = Course::with('students')->findOrFail($validated['course_id']);

        // Cek apakah course milik dosen
        $course = Course::findOrFail($validated['course_id']);
        if ($course->lecturer_id !== $user->id) {
            return response()->json([
                'message' => 'You can only add assignments to your own courses.',
            ]);
        }

        $assignment = Assignment::create($validated);

        // Kirim Notifikasi Email Ke Mahasiswa
        if ($course->students && $course->students->count() > 0) {
            foreach ($course->students as $student) {
                Mail::to($student->email)->send(new NewAssignmentNotification($assignment));
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Assignment created and notifications sent successfully.',
            'data' => $assignment
        ], 201);
    }

    //
}
