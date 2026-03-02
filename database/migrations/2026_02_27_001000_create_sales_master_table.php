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
        Schema::create('sales_master', function (Blueprint $table) {
            $table->id();
            $table->date('sale_date');
            $table->string('invoice_no', 30)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->decimal('total_amount', 14, 2)->default(0);

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
        Schema::dropIfExists('sales_master');
    }
};
