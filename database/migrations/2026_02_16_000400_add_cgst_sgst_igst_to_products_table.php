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
            $table->decimal('cgst_percent', 5, 2)->default(0)->after('gst_percent');
            $table->decimal('sgst_percent', 5, 2)->default(0)->after('cgst_percent');
            $table->decimal('igst_percent', 5, 2)->default(0)->after('sgst_percent');
        });

        DB::table('products')
            ->select('id', 'gst_percent')
            ->orderBy('id')
            ->chunk(200, function ($products): void {
                foreach ($products as $product) {
                    $gst = (float) ($product->gst_percent ?? 0);
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'cgst_percent' => round($gst / 2, 2),
                            'sgst_percent' => round($gst / 2, 2),
                            'igst_percent' => 0,
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
            $table->dropColumn(['cgst_percent', 'sgst_percent', 'igst_percent']);
        });
    }
};

