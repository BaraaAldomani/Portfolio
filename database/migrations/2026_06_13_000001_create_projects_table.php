<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('slug_ar')->unique();
            $table->string('slug_en')->unique();
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('sector_ar', 120);
            $table->string('sector_en', 120);
            $table->string('summary_ar', 500);
            $table->string('summary_en', 500);
            $table->text('problem_ar');
            $table->text('problem_en');
            $table->text('solution_ar');
            $table->text('solution_en');
            $table->text('result_ar');
            $table->text('result_en');
            // Client-facing capability highlights (NOT a raw tech stack).
            $table->json('highlights_ar');
            $table->json('highlights_en');
            $table->boolean('featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('projects');
    }
};
