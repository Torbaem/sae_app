<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyShippingStatusColumnInOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Eliminar la columna existente
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_status');
        });

        // Agregar la columna nuevamente con la configuración deseada
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_status', 255)->default('Pendiente')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar la columna (rollback)
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_status');
        });

        // Re-crear la columna con las configuraciones previas
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_status', 255)->default('Pendiente')->nullable()->after('status');
        });
    }
}
