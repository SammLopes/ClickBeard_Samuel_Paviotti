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
        Schema::create('barber_specialty', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('barber_id');
            $table->unsignedBigInteger('specialty_id');

            $table->integer('experience_years');
            $table->boolean('is_primary');

            $table->timestamps();

            $table->foreign('barber_id')
                ->references('id')
                ->on('barbers');

            $table->foreign('specialty_id')
                ->references('id')
                ->on('specialties')
                ->onDelete('cascade');

            $table->unique(['barber_id', 'specialty_id'], 'unique_barber_specialty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialties');
    }
};
