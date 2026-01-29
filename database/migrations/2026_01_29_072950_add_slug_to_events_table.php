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
        Schema::table('events', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        // Initialize slugs for existing events
        $events = \DB::table('events')->get();
        foreach ($events as $event) {
            $slug = \Illuminate\Support\Str::slug($event->title) . '-' . \Illuminate\Support\Str::random(5);
            \DB::table('events')->where('id', $event->id)->update(['slug' => $slug]);
        }

        // Change to not nullable and unique
        Schema::table('events', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
