<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Reply;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

class ReportController extends Controller
{
    /**
     * GET /api/reports/courses
     * Statistik jumlah mahasiswa per mata kuliah (untuk dosen)
     */
    public function coursesReport(Request $request)
    {
        $user = $request->user();

        if (!$user->isLecturer()) {
            return response()->json([
                'message' => 'Only lecturers can access this report'
            ], 403);
        }

        // Ambil semua course milik dosen
        $courses = Course::where('lecturer_id', $user->id)->withCount('students')->get(['id', 'name', 'lecturer_id']);

        $report = $courses->map(fn($course) => [
            'course_id' => $course->id,
            'course_name' => $course->name,
            'total_students' => $course->students_count
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $report
        ]);
    }

    /**
     * GET /api/reports/assignments
     * Statistik tugas yang sudah dan belum dinilai
     */

    public function assignmentsReport(Request $request)
    {
        $user = $request->user();

        if (!$user->isLecturer()) {
            return response()->json([
                'message' => 'Only lecturers can access this report'
            ], 403);
        }

        // Ambil tugas milik dosen
        $assignments = Assignment::whereHas('course', function ($q) use ($user) {
            $q->where('lecturer_id', $user->id);
        })
            ->withCount([
                'submissions as graded_count' => fn($q) => $q->whereNotNull('score'),
                'submissions as ungraded_count' => fn($q) => $q->whereNull('score')
            ])
            ->get(['id', 'title', 'course_id', 'deadline']);

        $report = $assignments->map(fn($a) => [
            'assignment_id' => $a->id,
            'title' => $a->title,
            'course_id' => $a->course_id,
            'graded_submissions' => $a->graded_count,
            'ungraded_submissions' => $a->ungraded_count
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $report
        ]);
    }

    /**
     * GET /api/reports/student/{id}
     * Statistik nilai dan tugas mahasiswa tertentu
     */
    public function studentReport(Request $request, $id)
    {
        $auth = $request->user();

        // Mahasiswa hanya bisa lihat dirinya sendiri
        if ($auth->isStudent() && $auth->id != $id) {
            return response()->json([
                'message' => 'Access denied.'
            ], 403);
        }

        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();

        // Ambil data tugas yang sudah disubmit oleh mahasiswa
        $submissions = Submission::where('student_id', $student->id)
        ->with(['assignment:id,title,course_id', 'assignment.course:id,name'])
        ->get(['id', 'assignment_id', 'score']);

        // Hitung total, rata-rata, dan jumlah belum dinilai
        $totalAssignments = $submissions->count();
        $graded = $submissions->whereNotNull('score');
        $ungraded = $submissions->whereNull('score');

        $averageScore = $graded->avg('score');

        $report = [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'total_assignments' => $totalAssignments,
            'graded' => $graded->count(),
            'ungraded' => $ungraded->count(),
            'average_score' => $averageScore ? round($averageScore, 2) : null,
            'submissions' => $submissions->map(fn($s) => [
                'assignment' => $s->assignment->title,
                'course' => $s->assignment->course->name,
                'score' => $s->score
            ])
            ];

            return response()->json([
                'status' => 'success',
                'data' => $report
            ]);
    }

    //
}
