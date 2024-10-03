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
        Schema::create('payment_info', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->decimal('price', 10, 2);
            $table->string('bank_info')->nullable();
            $table->string('payment_QR_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_info');
    }
};
