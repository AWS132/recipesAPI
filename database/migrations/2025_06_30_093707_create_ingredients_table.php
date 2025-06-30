<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->string('ingredient');
            $table->float('quantity', 8, 2);
            $table->timestamps();
            //composite key to prevent duplications
            $table->unique(['recipe_id', 'ingredient_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ingredients');
    }
};
