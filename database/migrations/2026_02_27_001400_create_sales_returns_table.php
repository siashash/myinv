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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales_master')->cascadeOnDelete();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('sale_invoice_no', 30);
            $table->string('return_no', 30)->unique();
            $table->date('return_date');
            $table->decimal('total_return_amount', 14, 2)->default(0);

            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
