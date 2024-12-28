<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeroContentTable extends Migration
{
    public function up()
    {
        Schema::create('hero_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Título del Hero');
            $table->text('description')->nullable()->comment('Descripción del Hero');
            $table->string('url')->nullable()->comment('Enlace del botón en Hero');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hero_contents');
    }
}
