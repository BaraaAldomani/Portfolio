<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fields the redesigned work showcase needs: a short brand name for big
 * display type, what Baraa actually built, one headline fact, a live link,
 * a phone screenshot, the kind of engagement, and which bespoke "world"
 * presents the project on the home page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->string('name_ar', 80)->nullable()->after('slug_en');
            $table->string('name_en', 80)->nullable()->after('name_ar');
            $table->string('kind', 20)->default('client')->after('name_en'); // gov | product | client | lab
            // Bespoke home-page presentation (resources/views/home/worlds/{scene}.blade.php).
            $table->string('scene', 40)->nullable()->after('kind');
            $table->string('year', 20)->nullable()->after('kind');
            $table->string('role_ar', 200)->nullable()->after('summary_en');
            $table->string('role_en', 200)->nullable()->after('role_ar');
            $table->string('stat_ar', 120)->nullable()->after('role_en');
            $table->string('stat_en', 120)->nullable()->after('stat_ar');
            $table->string('live_url')->nullable()->after('stat_en');
            $table->string('mobile_image_path')->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropColumn(['name_ar', 'name_en', 'kind', 'scene', 'year', 'role_ar', 'role_en', 'stat_ar', 'stat_en', 'live_url', 'mobile_image_path']);
        });
    }
};
