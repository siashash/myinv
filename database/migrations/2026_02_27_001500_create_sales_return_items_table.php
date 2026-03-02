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
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->constrained('sales_returns')->cascadeOnDelete();
            $table->foreignId('sale_detail_id')->constrained('sales_details')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_code', 100)->nullable();
            $table->string('product_name');
            $table->string('uom', 50)->nullable();
            $table->decimal('sale_qty', 14, 3)->default(0);
            $table->decimal('return_qty', 14, 3)->default(0);
            $table->decimal('rate', 14, 2)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
    }
};
