<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->integer('total_check_ins')->default(0);
            $table->integer('on_time_check_ins')->default(0);
            $table->integer('late_check_ins')->default(0);
            $table->integer('early_check_ins')->default(0);
            $table->integer('early_departures')->default(0);
            $table->float('total_hours_worked')->default(0);
            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            $table->float('attendance_percentage')->default(0);
            $table->enum('attendance_rating', ['POOR', 'FAIR', 'GOOD', 'EXCELLENT'])->default('POOR');
            $table->integer('average_arrival_minutes')->nullable();
            $table->integer('average_departure_minutes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_metrics');
    }
};