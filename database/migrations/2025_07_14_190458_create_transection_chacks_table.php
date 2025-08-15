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
        Schema::create('transection_chacks', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->longText('order');
            $table->longText('success_url');
            $table->string('if_old')->default('no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transection_chacks');
    }
};
