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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchase_master')->cascadeOnDelete();
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_name');
            $table->string('supplier_inv_no', 100)->nullable();
            $table->string('credit_note_no', 100)->unique();
            $table->date('return_date');
            $table->decimal('total_credit_amount', 14, 2)->default(0);
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
        Schema::dropIfExists('purchase_returns');
    }
};
