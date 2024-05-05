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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('original_quantity');
            $table->integer('original_parts');
            $table->integer('available_quantity');
            $table->integer('available_parts')->default(0);
            $table->integer('sold_quantity')->default(0);
            $table->integer('sold_parts')->default(0);
            $table->integer('parts_per_unit')->nullable();
            $table->float('discount')->default(0);
            $table->float('discount_limit')->default(0);
            $table->date('expire_date')->nullable();
            $table->double('price');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('supplier_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->noActionOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
