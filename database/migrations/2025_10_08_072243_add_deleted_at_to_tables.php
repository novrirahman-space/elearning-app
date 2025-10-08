<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'courses',
            'materials',
            'assignments',
            'discussions',
            'replies',
            'submissions',
            'users'
        ];

        foreach ($tables as $name) {
            if (!Schema::hasColumn($name, 'deleted_at')) {
                Schema::table($name, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'courses',
            'materials',
            'assignments',
            'discussions',
            'replies',
            'submissions',
            'users'
        ];

        foreach ($tables as $name) {
            if (!Schema::hasColumn($name, 'deleted_at')) {
                Schema::table($name, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
