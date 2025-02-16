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
        Schema::create('steadfast_couriers', function (Blueprint $table) {
            $table->id();
            $table->string('consignment_id')->unique();
            $table->string('invoice')->nullable();
            $table->string('tracking_code')->unique();
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('recipient_address');
            $table->decimal('cod_amount', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('steadfast_couriers');
    }
};
