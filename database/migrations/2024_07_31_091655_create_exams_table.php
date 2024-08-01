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
        Schema::create('exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable(false);
            $table->text('description');
            $table->integer('questions_count')->nullable(false);;
            $table->text('questions');
            $table->boolean('is_handled')->default(false);
            $table->timestamp('start_time')->nullable(false);
            $table->timestamp('end_time')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
