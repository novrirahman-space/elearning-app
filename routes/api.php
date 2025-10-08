<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\DiscussionController;
use App\Http\Controllers\Api\ReplyController;

Route::middleware('auth:sanctum')->group(function () {
    // Dosen & Mahasiswa bisa buat diskusi
    Route::post('/discussions', [DiscussionController::class, 'store']);

    // Dosen & Mahasiswa bisa balas diskusi
    Route::post('/discussions/{id}/replies', [ReplyController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Dosen buat tugas
    Route::middleware('role:lecturer')->post('/assignments', [AssignmentController::class, 'store']);

    // Mahasiswa unggah jawaban
    Route::middleware('role:student')->post('/submissions', [SubmissionController::class, 'store']);

    // Dosen beri nilai
    Route::middleware('role:lecturer')->post('/submissions/{id}/grade', [SubmissionController::class, 'grade']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Dosen upload materi
    Route::middleware('role:lecturer')->post('/materials', [MaterialController::class, 'store']);

    // Mahasiswa download materi
    Route::middleware('role:student')->get('/materials/{id}/download', [MaterialController::class, 'download']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/courses', [CourseController::class, 'index']);

    // lecturer only
    Route::middleware('role:lecturer')->group(function () {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{id}', [CourseController::class, 'update']);
        Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
    });

    // student only
    Route::middleware('role:student')->post('/courses/{id}/enroll', [CourseController::class, 'enroll']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
