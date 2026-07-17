<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table): void {
            $table->id();
            $table->string('role_ar');
            $table->string('role_en');
            $table->string('org_ar');
            $table->string('org_en');
            $table->string('period_ar', 60);
            $table->string('period_en', 60);
            $table->string('url')->nullable();
            $table->text('blurb_ar');
            $table->text('blurb_en');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('experiences');
    }
};
