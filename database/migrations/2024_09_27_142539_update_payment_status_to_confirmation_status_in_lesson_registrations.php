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
        Schema::table('lesson_registrations', function (Blueprint $table) {
            $table->renameColumn('payment_status', 'confirmation_status');
            $table->enum('confirmation_status', ['Confirmed', 'Pending', 'Canceled'])
                ->default('Pending')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_registrations', function (Blueprint $table) {
            $table->renameColumn('confirmation_status', 'payment_status');
            $table->enum('payment_status', ['paid', 'unpaid', 'canceled'])
                ->default('unpaid')
                ->change();
        });
    }
};
