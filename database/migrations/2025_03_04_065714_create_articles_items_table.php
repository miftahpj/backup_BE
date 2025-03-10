<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->string('title', 255);
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('image', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->text('template')->nullable();
            $table->timestamps();

        });  
    }
    
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_items');
    }
};