<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metrics', function (Blueprint $table): void {
            $table->id();
            // Which surface the metric belongs to: "home" or "services".
            $table->string('context')->default('home');
            $table->integer('value')->default(0);
            $table->string('suffix', 8)->nullable();
            $table->string('label_ar');
            $table->string('label_en');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('metrics');
    }
};
