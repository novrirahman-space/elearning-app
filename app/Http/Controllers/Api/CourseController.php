<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use SebastianBergmann\LinesOfCode\Counter;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('lecturer:id,name,email')->get();

        return response()->json([
            'status' => 'success',
            'data' => $courses
        ]);
    }

    // POST /api/courses (lecturer only)
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isLecturer()) {
            return response()->json([
                'message' => 'Only lecturers can create courses.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $course = Course::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'lecturer_id' => $user->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Course created successfully.',
            'data' => $course
        ], 201);
    }

    // PUT /api/courses/{id} (lecturer only)
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $course = Course::findOrFail($id);

        if ($user->id !== $course->lecturer_id){
            return response()->json([
                'message' => 'You can only edit your own courses'
            ], 403);
        }

        $validate = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $course->update($validate);

        return response()->json([
            'status' => 'success',
            'message' => 'Course updated successfully',
            'data' => $course
        ]);
    }

    // DELETE /api/courses/{id} (lecturer only)
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $course = Course::findOrFail($id);

        if ($user->id !== $course->lecturer_id) {
            return response()->json([
                'message' => 'You can only delete your own courses.'
            ], 403);
        }

        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully.'
        ]);
    }

    // POST /api/courses/{id}/enroll (student only)
    public function enroll(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->isStudent()) {
            return response()->json([
                'message' => 'Only students can enroll'
            ], 403);
        }

        $course = Course::findOrFail($id);

        if ($user->coursesEnrolled()->where('course_id', $id)->exists()){
            return response()->json([
                'message' => 'You are already enrolled'
            ], 409);
        }

        $user->coursesEnrolled()->attach($course->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Enrollment successful.'
        ], 201);
    }

    //
}
