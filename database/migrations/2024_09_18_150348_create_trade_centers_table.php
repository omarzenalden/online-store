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
        Schema::create('trade_centers', function (Blueprint $table) {
            $table->id('id');
            $table->string('product_name');
            $table->integer('memory');
            $table->integer('battery_percentage');
            $table->string('brand_name');
            $table->date('date_of_manufacture');
            $table->string('addition_notes');

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('statuses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_centers');
    }
};
