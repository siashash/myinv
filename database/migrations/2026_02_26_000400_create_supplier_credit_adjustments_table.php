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
        Schema::create('supplier_credit_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_credit_note_id')->constrained('supplier_credit_notes')->cascadeOnDelete();
            $table->unsignedBigInteger('supplier_id');
            $table->foreignId('purchase_id')->constrained('purchase_master')->cascadeOnDelete();
            $table->decimal('adjusted_amount', 14, 2)->default(0);
            $table->date('adjusted_date');
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
        Schema::dropIfExists('supplier_credit_adjustments');
    }
};
