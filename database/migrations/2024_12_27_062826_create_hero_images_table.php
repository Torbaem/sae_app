<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hero_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hero_content_id')->constrained('hero_contents')->onDelete('cascade');
            $table->string('image_path');
            $table->string('type'); // para distinguir entre 'background_image' y 'product_image'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hero_images');
    }
};