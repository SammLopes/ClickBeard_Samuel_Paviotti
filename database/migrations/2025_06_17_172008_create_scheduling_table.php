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
        Schema::create('schedulings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('barber_id');
            $table->unsignedBigInteger('service_id');
            
            $table->date('scheduling_date');
            $table->time('scheduling_time');
            
            $table->enum('status', [
                'scheduled', 
                'confirmed', 
                'completed', 
                'cancelled'
            ])->default('scheduled');
            
            $table->text('notes')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');
                  
            $table->foreign('barber_id')
                  ->references('id')
                  ->on('barbers');
                  
            $table->foreign('service_id')
                  ->references('id')
                  ->on('services');
                  
            $table->unique([
                'barber_id', 
                'scheduling_date', 
                'scheduling_time'
            ], 'unique_scheduling');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduling');
    }
};
