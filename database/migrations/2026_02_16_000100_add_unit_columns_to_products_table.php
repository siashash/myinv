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
            $table->foreignId('base_unit_id')->nullable()->after('uom')->constrained('units')->nullOnDelete();
            $table->foreignId('sale_unit_id')->nullable()->after('base_unit_id')->constrained('units')->nullOnDelete();
            $table->decimal('conversion_factor', 12, 4)->default(1)->after('sale_unit_id');
        });

        $unitMap = DB::table('units')
            ->select('id', 'short_name')
            ->get()
            ->keyBy(fn ($unit) => strtoupper(trim((string) $unit->short_name)));

        DB::table('products')
            ->select('id', 'uom')
            ->orderBy('id')
            ->chunk(200, function ($products) use ($unitMap): void {
                foreach ($products as $product) {
                    $uom = strtoupper(trim((string) ($product->uom ?? '')));
                    $unit = $unitMap[$uom] ?? null;

                    if ($unit) {
                        DB::table('products')
                            ->where('id', $product->id)
                            ->update([
                                'base_unit_id' => $unit->id,
                                'sale_unit_id' => $unit->id,
                                'conversion_factor' => 1,
                            ]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('base_unit_id');
            $table->dropConstrainedForeignId('sale_unit_id');
            $table->dropColumn('conversion_factor');
        });
    }
};
