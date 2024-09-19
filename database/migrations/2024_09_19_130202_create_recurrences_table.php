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
        Schema::create('recurrences', function (Blueprint $table) {
            $table->id();
            $table->enum('frequency', ['weekly', 'bi-weekly', 'monthly']);
            $table->integer('interval');
            $table->string('days_of_week')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date');
            $table->foreignId('lesson_id')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurrences');
    }
};
