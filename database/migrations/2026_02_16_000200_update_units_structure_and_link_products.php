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
        Schema::table('units', function (Blueprint $table) {
            $table->string('base_unit', 50)->nullable()->after('id');
            $table->string('sales_unit', 50)->nullable()->after('base_unit');
            $table->decimal('conversion_factor', 12, 4)->default(1)->after('sales_unit');
        });

        DB::table('units')
            ->whereNull('base_unit')
            ->orderBy('id')
            ->chunk(200, function ($units): void {
                foreach ($units as $unit) {
                    $base = strtoupper(trim((string) ($unit->short_name ?: $unit->unit_name)));

                    DB::table('units')
                        ->where('id', $unit->id)
                        ->update([
                            'base_unit' => $base,
                            'sales_unit' => $base,
                            'conversion_factor' => 1,
                        ]);
                }
            });

        $defaults = [
            ['base_unit' => 'KG', 'sales_unit' => 'GRAM', 'conversion_factor' => 1000],
            ['base_unit' => 'BOX', 'sales_unit' => 'PIECE', 'conversion_factor' => 10],
        ];

        foreach ($defaults as $default) {
            $exists = DB::table('units')
                ->where('base_unit', $default['base_unit'])
                ->where('sales_unit', $default['sales_unit'])
                ->where('conversion_factor', $default['conversion_factor'])
                ->exists();

            if (! $exists) {
                DB::table('units')->insert([
                    'unit_name' => $default['base_unit'] . ' to ' . $default['sales_unit'],
                    'short_name' => $default['base_unit'] . '_' . $default['sales_unit'],
                    'base_unit' => $default['base_unit'],
                    'sales_unit' => $default['sales_unit'],
                    'conversion_factor' => $default['conversion_factor'],
                ]);
            }
        }

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('uom')->constrained('units')->nullOnDelete();
            $table->string('sales_uom', 50)->nullable()->after('uom');
        });

        DB::table('products')
            ->orderBy('id')
            ->chunk(200, function ($products): void {
                foreach ($products as $product) {
                    $base = strtoupper(trim((string) ($product->uom ?? '')));
                    $sales = strtoupper(trim((string) ($product->sales_uom ?? '')));

                    if ($sales === '') {
                        $sales = $base;
                    }

                    $unit = DB::table('units')
                        ->where('base_unit', $base)
                        ->where('sales_unit', $sales)
                        ->where('conversion_factor', (float) ($product->conversion_factor ?? 1))
                        ->first();

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'sales_uom' => $sales,
                            'unit_id' => $unit?->id,
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
            $table->dropConstrainedForeignId('unit_id');
            $table->dropColumn('sales_uom');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['base_unit', 'sales_unit', 'conversion_factor']);
        });
    }
};
