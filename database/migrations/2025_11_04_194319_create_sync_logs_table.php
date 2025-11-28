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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->enum('type', ['daily', 'monthly']);
            $table->timestamp('sync_date');
            $table->date('last_data_date')->nullable();
            $table->integer('inserted')->default(0);
            $table->integer('updated')->default(0);
            $table->enum('status', ['processing', 'success', 'failed'])->default('processing');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
