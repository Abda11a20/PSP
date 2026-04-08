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
        Schema::table('phishing_pages', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
            $table->string('seo_title')->nullable()->after('description');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('seo_keywords')->nullable()->after('seo_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phishing_pages', function (Blueprint $table) {
            $table->dropColumn(['slug', 'seo_title', 'seo_description', 'seo_keywords']);
        });
    }
};
