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
        Schema::create('image_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->constrained()->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->boolean('sun')->default(false);
            $table->boolean('mon')->default(true);
            $table->boolean('tue')->default(true);
            $table->boolean('wed')->default(true);
            $table->boolean('thu')->default(true);
            $table->boolean('fri')->default(true);
            $table->boolean('sat')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_schedule');
    }
};
