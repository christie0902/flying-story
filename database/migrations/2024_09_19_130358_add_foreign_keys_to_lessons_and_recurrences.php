<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->foreign('recurrence_id')->references('id')->on('recurrences')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->dropForeign(['lesson_id']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['recurrence_id']);
        });
    }
};
