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
        Schema::create('indicators_monthly', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->timestamp('date');
            $table->string('company');
            $table->integer('year');
            $table->integer('month');
            $table->string('indicator');
            $table->text('description')->nullable();
            $table->decimal('target', 15, 4)->default(0);
            $table->decimal('actual', 15, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicators_monthly');
    }
};
