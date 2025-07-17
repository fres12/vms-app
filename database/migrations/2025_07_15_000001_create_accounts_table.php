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
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('no_employee', 100);
            $table->string('name', 255);
            $table->string('position', 255)->nullable();
            $table->unsignedBigInteger('deptID');
            $table->timestamps();

            $table->foreign('deptID')->references('deptID')->on('depts')->onDelete('cascade');
        });

        // Drop picdepts table if exists
        if (Schema::hasTable('picdepts')) {
            Schema::drop('picdepts');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
}; 