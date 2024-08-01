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
        Schema::create('prize_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('exam_id')->nullable(false);
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->uuid('prize_id')->nullable(false);
            $table->foreign('prize_id')->references('id')->on('prizes')->onDelete('cascade');
            $table->uuid('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_records');
    }
};
