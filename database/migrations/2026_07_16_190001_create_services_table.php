<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('icon_key')->nullable();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('description_ar');
            $table->text('description_en');
            $table->json('points_ar');
            $table->json('points_en');
            $table->string('tag_ar')->nullable();
            $table->string('tag_en')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_alt_ar')->nullable();
            $table->string('image_alt_en')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('services');
    }
};
