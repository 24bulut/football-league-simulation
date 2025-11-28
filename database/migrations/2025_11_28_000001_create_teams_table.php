<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->integer('power')->default(0); // 1-100 strength rating
            $table->decimal('home_advantage', 3, 2)->default(0.0); // multiplier e.g., 1.15
            $table->decimal('goalkeeper_factor', 3, 2)->default(0.0); // defensive modifier
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};

