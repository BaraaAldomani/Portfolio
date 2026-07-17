<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('process_steps', function (Blueprint $table): void {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('blurb_ar');
            $table->text('blurb_en');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('process_steps');
    }
};
