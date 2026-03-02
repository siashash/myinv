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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales_master')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_code', 100)->nullable();
            $table->string('product_name');
            $table->string('uom', 50)->nullable();
            $table->decimal('qty', 14, 3)->default(0);
            $table->decimal('rate', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_details');
    }
};
