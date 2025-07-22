<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email');
            $table->string('nik');
            $table->string('idcardphoto');
            $table->string('selfphoto');
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('deptpurpose');
            $table->text('visit_purpose');
            $table->dateTime('startdate');
            $table->dateTime('enddate');
            $table->string('equipment_type')->nullable();
            $table->string('brand')->nullable();
            $table->string('status')->default('For Review');
            $table->dateTime('submit_date');
            $table->dateTime('approved_date')->nullable();
            $table->string('ticket_number')->nullable();
            $table->text('barcode')->nullable();

            $table->foreign('deptpurpose')->references('deptID')->on('depts');
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