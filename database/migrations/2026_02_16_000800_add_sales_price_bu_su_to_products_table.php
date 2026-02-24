<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('sales_price_bu', 12, 2)->default(0)->after('purchase_price');
            $table->decimal('sales_price_su', 12, 2)->default(0)->after('sales_price_bu');
        });

        DB::table('products')
            ->select('id', 'purchase_price', 'conversion_factor')
            ->orderBy('id')
            ->chunk(200, function ($products): void {
                foreach ($products as $product) {
                    $purchasePrice = (float) ($product->purchase_price ?? 0);
                    $factor = (float) ($product->conversion_factor ?? 1);
                    if ($factor <= 0) {
                        $factor = 1;
                    }

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'sales_price_bu' => round($purchasePrice, 2),
                            'sales_price_su' => round($purchasePrice / $factor, 2),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sales_price_bu', 'sales_price_su']);
        });
    }
};

