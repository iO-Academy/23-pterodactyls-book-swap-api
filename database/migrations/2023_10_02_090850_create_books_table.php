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
            $table->binary('claimed')->default(0);
            $table->integer('genre_id');
            $table->integer('page_count');
            $table->string('claimed_by')->nullable();
            $table->string('image')->nullable();
            $table->year('year');
            $table->integer('review_id')->nullable();
            $table->string('email_of_owner')->nullable();
            $table->string('name_of_owner')->nullable();
            $table->binary('deleted')->default(0);
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
