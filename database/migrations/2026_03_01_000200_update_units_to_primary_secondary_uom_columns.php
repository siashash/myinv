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
        if (! Schema::hasColumn('units', 'prim_uom')) {
            Schema::table('units', function (Blueprint $table) {
                $table->string('prim_uom', 50)->nullable();
            });
        }

        if (! Schema::hasColumn('units', 'prim_uom_conv')) {
            Schema::table('units', function (Blueprint $table) {
                $table->decimal('prim_uom_conv', 12, 4)->nullable();
            });
        }

        if (! Schema::hasColumn('units', 'sec_uom')) {
            Schema::table('units', function (Blueprint $table) {
                $table->string('sec_uom', 50)->nullable();
            });
        }

        if (! Schema::hasColumn('units', 'sec_uom_conv')) {
            Schema::table('units', function (Blueprint $table) {
                $table->decimal('sec_uom_conv', 12, 4)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasPrimUom = Schema::hasColumn('units', 'prim_uom');
        $hasPrimUomConv = Schema::hasColumn('units', 'prim_uom_conv');
        $hasSecUom = Schema::hasColumn('units', 'sec_uom');
        $hasSecUomConv = Schema::hasColumn('units', 'sec_uom_conv');

        if ($hasPrimUom || $hasPrimUomConv || $hasSecUom || $hasSecUomConv) {
            Schema::table('units', function (Blueprint $table) use ($hasPrimUom, $hasPrimUomConv, $hasSecUom, $hasSecUomConv) {
                $columns = [];

                if ($hasPrimUom) {
                    $columns[] = 'prim_uom';
                }
                if ($hasPrimUomConv) {
                    $columns[] = 'prim_uom_conv';
                }
                if ($hasSecUom) {
                    $columns[] = 'sec_uom';
                }
                if ($hasSecUomConv) {
                    $columns[] = 'sec_uom_conv';
                }

                $table->dropColumn($columns);
            });
        }
    }
};
