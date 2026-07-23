<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('check_in_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('department_id')->unique();
            $table->time('standard_time')->default('08:00');
            $table->integer('grace_minutes')->default(15);
            $table->integer('late_threshold_minutes')->default(30);
            $table->timestamps();

            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('check_in_settings');
    }
};