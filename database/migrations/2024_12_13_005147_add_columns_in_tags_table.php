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
        Schema::table('tags', function (Blueprint $table) {
            $table->string('title');
            $table->softDeletes();

            $table->integer('count')->nullable(); // для изменений и удалений
            $table->text('count_text')->nullable(); // для изменений и удалений
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropSoftDeletes();
            $table->dropColumn('count');
            $table->dropColumn('count_text');
        });
    }
};
