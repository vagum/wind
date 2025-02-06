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
        Schema::table('post_profile_views', function (Blueprint $table) {

            // Удаляем внешний ключ перед изменением столбца
            $table->dropForeign(['profile_id']);

            // Изменяем поле profile_id на nullable
            $table->foreignId('profile_id')->nullable()->change();

            // Восстанавливаем внешний ключ
            $table->foreign('profile_id')
                ->references('id')
                ->on('profiles')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_profile_views', function (Blueprint $table) {
            // Удаляем внешний ключ
            $table->dropForeign(['profile_id']);

            // Возвращаем поле profile_id к состоянию NOT NULL
            $table->foreignId('profile_id')->nullable(false)->change();

            // Восстанавливаем внешний ключ
            $table->foreign('profile_id')
                ->references('id')
                ->on('profiles')
                ->onDelete('cascade');
        });
    }
};
