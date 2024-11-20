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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->enum('like_status',['like','dislike','neutral'])->default('neutral');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // user who liked the review
            $table->foreignId('review_id')->constrained()->onDelete('cascade'); // review being liked
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
