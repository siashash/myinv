<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'unit_id')) {
            return;
        }

        DB::table('products')
            ->select('id', 'purchase_price', 'unit_id', 'uom', 'sales_uom')
            ->orderBy('id')
            ->chunk(200, function ($products): void {
                foreach ($products as $product) {
                    $unit = null;
                    if (! empty($product->unit_id)) {
                        $unit = DB::table('units')
                            ->where('id', $product->unit_id)
                            ->first([
                                'prim_uom',
                                'prim_uom_conv',
                                'sec_uom',
                                'sec_uom_conv',
                                'base_unit',
                                'sales_unit',
                                'conversion_factor',
                            ]);
                    }

                    $primaryUom = strtoupper(trim((string) ($unit->prim_uom ?? $unit->base_unit ?? $product->uom ?? '')));
                    $secondaryUom = strtoupper(trim((string) ($unit->sec_uom ?? $unit->sales_unit ?? $product->sales_uom ?? '')));

                    if ($primaryUom === '') {
                        $primaryUom = strtoupper(trim((string) ($product->uom ?? '')));
                    }
                    if ($secondaryUom === '') {
                        $secondaryUom = strtoupper(trim((string) ($product->sales_uom ?? $primaryUom)));
                    }

                    $primaryConv = (float) ($unit->prim_uom_conv ?? 1);
                    if ($primaryConv <= 0) {
                        $primaryConv = 1.0;
                    }

                    $secondaryConv = (float) ($unit->sec_uom_conv ?? $unit->conversion_factor ?? 1);
                    if ($secondaryConv <= 0) {
                        $secondaryConv = 1.0;
                    }

                    $purchasePrice = (float) ($product->purchase_price ?? 0);

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'uom' => $primaryUom,
                            'sales_uom' => $secondaryUom,
                            'sales_price_bu' => round($purchasePrice * $primaryConv, 2),
                            'sales_price_su' => round($purchasePrice * $secondaryConv, 2),
                            'conversion_factor' => round($secondaryConv / $primaryConv, 4),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: data recalculation is intentionally not reversed.
    }
};
