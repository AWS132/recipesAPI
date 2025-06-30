<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recipe', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('steps')->nullable();
            //total nutritional value details
            $table->float('total_carbs', 8, 2)->default(0);
            $table->float('total_fat', 8, 2)->default(0);
            $table->float('total_protein', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipe');
    }
};
