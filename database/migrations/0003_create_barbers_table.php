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
        Schema::create('barbers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);  
            $table->string('email', 255)->unique();  
            $table->integer('age')->unsigned();
            $table->string('phone', 20)->nullable();  
            $table->date('hire_date'); 
            $table->boolean('is_active')->default(true);  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barbers');
    }
};
