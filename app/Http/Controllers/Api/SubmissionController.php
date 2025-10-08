<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use App\Mail\GradedSubmissionNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Contracts\Service\Attribute\Required;

class SubmissionController extends Controller
{
    // POST /api/submissions
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isStudent()) {
            return response()->json([
                'message' => 'Only students can submit assignments.'
            ], 403);
        }

        $validated = $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'file' => 'required|file|max:10240'
        ]);

        $assignment = Assignment::with('course')->findOrFail($validated['assignment_id']);

        // Memastikan mahasiswa ikut di course tersebut
        if (!$user->coursesEnrolled()->where('course_id', $assignment->course_id)->exists()) {
            return response()->json(['message' => 'You are not enrolled in this course.'], 403);
        }

        // Simpan file jawaban
        $path = $request->file('file')->store('submissions', 'public');

        // Cek apakah sudah submit sebelumnya
        if (Submission::where('assignment_id', $assignment->id)->where('student_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You have already submitted this assignment.'
            ], 409);
        }

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $user->id,
            'file_path' => $path
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Submission uploaded successfully',
            'data' => $submission
        ], 201);
    }

    // POST api/submissions/{id}/grade
    public function grade(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->isLecturer()) {
            return response()->json([
                'message' => 'Only lecturers can grade submissions'
            ]);
        }

        $submission = Submission::with('assignment.course')->findOrFail($id);

        // cek apakah dosen pemilik course
        if ($submission->assignment->course->lecturer_id !== $user->id) {
            return response()->json([
                'message' => 'You cannot grade submissions for another lecturer\'s course'
            ], 403);
        }

        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100'
        ]);

        $submission->update([
            'score' => $validated['score']
        ]);

        // Kirim Notifikasi Nilai ke Mahasiswa
        Mail::to($submission->student->email)->send(new GradedSubmissionNotification($submission));

        return response()->json([
            'status' => 'success',
            'message' => 'Submission graded successfully',
            'data' => $submission
        ]);
    }

    //
}
