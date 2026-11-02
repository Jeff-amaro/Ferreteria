<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->foreignId('repartidor_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_despacho')->nullable()->after('delivery_date');
            $table->timestamp('fecha_entrega')->nullable()->after('fecha_despacho');
            $table->text('incidencia_nota')->nullable()->after('fecha_entrega');
            $table->string('codigo_confirmacion', 12)->nullable()->after('incidencia_nota');

            $table->index('codigo_confirmacion');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE sales_orders MODIFY status ENUM('pendiente', 'en_preparacion', 'despachado', 'en_camino', 'con_incidencia', 'entregado', 'anulado') NOT NULL DEFAULT 'pendiente'");
        }
    }

    public function down(): void
    {
        DB::table('sales_orders')
            ->whereIn('status', ['en_camino', 'con_incidencia'])
            ->update(['status' => 'despachado']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE sales_orders MODIFY status ENUM('pendiente', 'en_preparacion', 'despachado', 'entregado', 'anulado') NOT NULL DEFAULT 'pendiente'");
        }

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropForeign(['repartidor_id']);
            $table->dropIndex(['codigo_confirmacion']);
            $table->dropColumn([
                'repartidor_id',
                'fecha_despacho',
                'fecha_entrega',
                'incidencia_nota',
                'codigo_confirmacion',
            ]);
        });
    }
};
