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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('inspector_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('file_image')->nullable();
            $table->integer('quality_score');
            $table->text('comment')->nullable();
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('inspector_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
