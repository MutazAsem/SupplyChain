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
        Schema::disableForeignKeyConstraints();
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('farmer_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity',10,2)->default(1);
            $table->decimal('unit_price',10,2);
            $table->string('status');
            $table->unsignedBigInteger('address_id');
            $table->decimal('total_price',10,2);
            $table->unsignedBigInteger('delivery_id');
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('farmer_id')->references('id')->on('farms');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('address_id')->references('id')->on('addresses');
            $table->foreign('delivery_id')->references('id')->on('users');
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
