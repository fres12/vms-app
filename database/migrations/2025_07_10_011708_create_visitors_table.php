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
            $table->id();
            $table->string('nik');
            $table->text('id_card_photo')->nullable();
            $table->string('full_name', 255);
            $table->string('company', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('department_purpose', 255)->nullable();
            $table->string('section_purpose', 255)->nullable();
            $table->string('self_photo')->nullable();
            $table->dateTime('visit_datetime');
            $table->timestamps();
            $table->string('status', 255)->nullable();
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
