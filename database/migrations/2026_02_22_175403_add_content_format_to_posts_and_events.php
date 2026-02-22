<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('content_format', 20)->default('html')->after('content_en');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('content_format', 20)->default('html')->after('description_en');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('content_format');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('content_format');
        });
    }
};
