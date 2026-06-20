<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            // Public-relative path to the cover image (e.g. images/projects/x.svg
            // or images/projects/x.jpg). Nullable: when empty the model falls
            // back to the slug-based placeholder. Edit this per row to swap the
            // image later without touching code.
            $table->string('image_path')->nullable()->after('slug_en');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropColumn('image_path');
        });
    }
};
