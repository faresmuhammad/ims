<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code')->unique();
            $table->dateTime('date')->default(now());
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->noActionOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete()->noActionOnDelete();
            $table->double('total_amount')->default(0);
            $table->float('discount')->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
