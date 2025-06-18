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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);  
            $table->text('description')->nullable(); 
            $table->unsignedBigInteger('specialty_id')->nullable();
            $table->decimal('price', 8, 2);  
            $table->integer('duration_minutes');  
            $table->boolean('is_active')->default(true);  
            $table->timestamps();

            $table->foreign('specialty_id')
                ->references('id')
                ->on('specialties')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
