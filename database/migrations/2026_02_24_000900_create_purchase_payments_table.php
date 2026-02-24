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
        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchase_master')->cascadeOnDelete();
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_name');
            $table->string('supplier_inv_no', 100)->nullable();
            $table->decimal('invoice_amount', 14, 2)->default(0);
            $table->decimal('payment_amount', 14, 2)->default(0);
            $table->enum('payment_mode', ['Cash', 'Cheque', 'UPI']);
            $table->date('payment_date');
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('supplier_id')
                ->on('suppliers')
                ->cascadeOnDelete();

            $table->index(['supplier_id', 'purchase_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
    }
};
