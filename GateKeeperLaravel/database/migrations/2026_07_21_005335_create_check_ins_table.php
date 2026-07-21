<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->timestamp('check_in_time')->useCurrent();
            $table->timestamp('check_out_time')->nullable();
            $table->enum('status', ['CHECKED_IN', 'CHECKED_OUT', 'LATE', 'ABSENT'])->default('CHECKED_IN');
            $table->boolean('is_late')->default(false);
            $table->integer('late_minutes')->nullable();
            $table->boolean('is_early')->default(false);
            $table->integer('early_minutes')->nullable();
            $table->boolean('is_early_departure')->default(false);
            $table->integer('early_departure_minutes')->nullable();
            $table->float('hours_worked')->nullable();
            $table->date('date')->default(now());
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->unique(['user_id', 'date']);
            $table->index(['user_id', 'date']);
            $table->index('date');
            $table->index('status');
            $table->index('is_late');
        });
    }

    public function down()
    {
        Schema::dropIfExists('check_ins');
    }
};