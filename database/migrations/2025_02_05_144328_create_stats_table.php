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
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->nullable();
            $table->integer('posts_count')->nullable();
            $table->integer('comments_count')->nullable();
            $table->integer('replies_count')->nullable();
            $table->integer('likes_count')->nullable();
            $table->integer('views_count')->nullable();
            $table->float('likes_views')->nullable();
            $table->float('likes_comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
