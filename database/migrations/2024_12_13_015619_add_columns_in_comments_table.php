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
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade'); // добавить index
            $table->unsignedBigInteger('profile_id'); // добавить index и fk
            $table->foreignId('parent_id')->index()->nullable()->constrained('comments');
            $table->text('condent'); // исправить опечатку

            $table->softDeletes();

            $table->string('number_text')->nullable(); // для удаления
            $table->float('number')->nullable(); // для удаления
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('post_id', 'profile_id', 'parent_id', 'condent', 'number_text', 'number');
            $table->dropSoftDeletes();
        });
    }
};
