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
        Schema::create('purchase_master', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_name');
            $table->string('supplier_inv_no', 100)->nullable();
            $table->date('purchase_date');
            $table->decimal('tot_taxable_amount', 14, 2)->default(0);
            $table->decimal('tot_gst_amount', 14, 2)->default(0);
            $table->decimal('invoice_amount', 14, 2)->default(0);
            $table->enum('purchase_mode', ['Cash', 'Credit', 'UPI']);

            $table->foreign('supplier_id')
                ->references('supplier_id')
                ->on('suppliers')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_master');
    }
};
