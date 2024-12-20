<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('editable_contents', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->comment('Identificador único de la sección');
            $table->string('page')->comment('Página donde se encuentra el contenido');
            $table->string('content_type')->comment('Tipo de contenido (texto, imagen, video)');
            $table->integer('item_order')->nullable()->comment('Orden del elemento en la sección');
            
            // Campos generales para diferentes tipos de contenido
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_type')->nullable()->comment('youtube, vimeo, local, iframe');
            
            // Campos para personalización adicional
            $table->json('additional_data')->nullable()->comment('JSON para datos adicionales');
            
            // Metadatos
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Claves foráneas para usuarios
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('editable_contents');
    }
};
