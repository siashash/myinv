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
        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained('purchase_returns')->cascadeOnDelete();
            $table->foreignId('purchase_detail_id')->constrained('purchase_details')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->string('uom', 50)->nullable();
            $table->decimal('purchase_qty', 14, 3)->default(0);
            $table->decimal('return_qty', 14, 3)->default(0);
            $table->decimal('rate', 14, 4)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->timestamps();

            $table->index('purchase_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_items');
    }
};
