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
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade'); // без index чтобы потом добавить
            $table->string('title')->unique();
            $table->unsignedBigInteger('category_id')->index(); // без foreignId чтобы потом добавить
            $table->string('image_path')->nullable();
            $table->string('description')->nullable(); // string вместо text
            $table->string('condent')->nullable(); // опечатка для исправления + string вместо text
            $table->dateTime('published_at')->nullable();
            $table->unsignedSmallInteger('status')->default(1);
            $table->boolean('is_published')->default(true);
//            $table->integer('views')->nullable(); // добавил подсчет в PostResource из post_profile_views

            $table->string('image_sd')->nullable(); // под удаление
            $table->text('description_ai')->nullable(); // под удаление

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('profile_id');
            $table->dropColumn('title');
            $table->dropColumn('category_id');
            $table->dropColumn('image_path');
            $table->dropColumn('description');
            $table->dropColumn('condent');
            $table->dropColumn('published_at');
            $table->dropColumn('status');
            $table->dropColumn('is_published');
            $table->dropColumn('image_sd');
            $table->dropColumn('description_ai');

            $table->dropSoftDeletes();

        });
    }
};
