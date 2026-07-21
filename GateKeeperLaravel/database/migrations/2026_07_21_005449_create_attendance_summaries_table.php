<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->integer('month');
            $table->integer('year');
            $table->integer('total_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->integer('on_time_days')->default(0);
            $table->integer('early_days')->default(0);
            $table->integer('early_departure_days')->default(0);
            $table->float('total_hours_worked')->default(0);
            $table->time('average_arrival_time')->nullable();
            $table->time('average_departure_time')->nullable();
            $table->float('attendance_percentage')->default(0);
            $table->float('punctuality_percentage')->default(0);
            $table->enum('attendance_rating', ['POOR', 'FAIR', 'GOOD', 'EXCELLENT'])->default('POOR');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->unique(['user_id', 'month', 'year']);
            $table->index(['user_id', 'year', 'month']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_summaries');
    }
};