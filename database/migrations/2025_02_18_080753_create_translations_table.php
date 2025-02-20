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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('meta_key')->unique();
            $table->text('content');
            $table->unsignedBigInteger('language_id');
            $table->unsignedBigInteger('tag_id')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');

            $table->index('language_id');
            $table->index('tag_id');
            $table->index('meta_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
