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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->binary('claimed')->default(0);
            $table->integer('page_count');
            $table->string('claimed_by_name')->nullable();
            $table->string('image')->nullable();
            $table->year('year');
            $table->integer('review_id')->nullable();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
