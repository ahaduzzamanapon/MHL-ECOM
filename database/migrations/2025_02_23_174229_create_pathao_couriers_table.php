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
        Schema::create('pathao_couriers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('merchant_order_id');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('recipient_address');
            $table->string('recipient_city');
            $table->string('recipient_zone');
            $table->string('recipient_area');
            $table->string('delivery_type');
            $table->string('item_type');
            $table->text('special_instruction')->nullable();
            $table->integer('item_quantity');
            $table->decimal('item_weight', 8, 2);
            $table->text('item_description')->nullable();
            $table->decimal('amount_to_collect', 10, 2);
            $table->string('consignment_id')->nullable();
            $table->string('order_status')->nullable();
            $table->decimal('delivery_fee', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pathao_couriers');
    }
};
