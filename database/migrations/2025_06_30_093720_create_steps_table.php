<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::create('steps', function (Blueprint $table) {
           $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->text('description');
            $table->integer('order');
            $table->timestamps();
            $table->unique(['recipe_id', 'order','id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('steps');
    }
};
