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
        Schema::table('stock', function (Blueprint $table) {
            $table->unsignedBigInteger('sale_id')->nullable()->after('purchase_id');
            $table->foreign('sale_id')->references('id')->on('sales_master')->nullOnDelete();
            $table->index('sale_id');
        });

        DB::statement('ALTER TABLE `stock` MODIFY `purchase_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `stock` MODIFY `supplier_id` BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `stock` MODIFY `supplier_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `stock` MODIFY `purchase_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('stock', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropIndex(['sale_id']);
            $table->dropColumn('sale_id');
        });
    }
};
