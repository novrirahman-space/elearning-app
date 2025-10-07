<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    // POST /api/materials (lecturer only)
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isLecturer()) {
            return response()->json([
                'message' => 'Only lecturers can upload materials.'
            ], 403);
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240'
        ]);

        // Cek apakah course milik dosen bersangkutan
        $course = Course::findOrFail($validated['course_id']);
        if ($course->lecturer_id !== $user->id) {
            return response()->json([
                'message' => 'You can only upload materials for your own courses.'
            ], 403);
        }

        // Simpan file ke storage
        $path = $request->file('file')->store('materials', 'public');

        //Simpan ke DB
        $material = Material::create([
            'course_id' => $validated['course_id'],
            'title' => $validated['title'],
            'file_path' => $path
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Material uploaded successfully',
            'data' => $material
        ], 201);
    }

    // GET /api/materials/{id}/download (student only)
    public function download(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->isStudent()) {
            return response()->json([
                'message' => 'Only students can download materials.'
            ], 403);
        }

        $material = Material::findOrFail($id);

        // Cek mahasiswa terdaftar di course tersebut
        if (!$user->coursesEnrolled()->where('course_id', $material->course_id)->exists()){
            return response()->json([
                'message' => 'You are not enrolled in this course.'
            ], 403);
        }

        $filePath = storage_path('app/public/' . $material->file_path);

        if (!file_exists($filePath)) {
            return response()->json([
                'message' => 'File not found.'
            ], 404);
        }

        return response()->download($filePath, $material->title . '.' . pathinfo($material->file_path, PATHINFO_EXTENSION));
    }

    //
}
