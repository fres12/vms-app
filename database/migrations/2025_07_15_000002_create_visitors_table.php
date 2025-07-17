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
        Schema::create('visitors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fullname');
            $table->string('email');
            $table->string('nik');
            $table->string('idcardphoto');
            $table->string('selfphoto');
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('deptpurpose')->nullable();
            $table->string('visit_purpose');
            $table->dateTime('startdate');
            $table->dateTime('enddate');
            $table->string('equipment_type')->nullable();
            $table->string('brand')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('submit_date')->nullable();
            $table->dateTime('approved_date')->nullable();

            $table->foreign('deptpurpose')->references('deptID')->on('depts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
}; 