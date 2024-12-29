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
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->unsignedBigInteger('user_id'); // без foreignId и index, чтобы потом добавить
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->text('description')->nullable();
            $table->string('ginder')->nullable(); // с опечаткой для исправления
            $table->dateTime('birthed_at')->nullable();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('user_id');
            $table->dropColumn('address');
            $table->dropColumn('phone');
            $table->dropColumn('avatar');
            $table->dropColumn('description');
            $table->dropColumn('ginder');
            $table->dropColumn('birthed_at');
            $table->dropSoftDeletes();
        });
    }
};
