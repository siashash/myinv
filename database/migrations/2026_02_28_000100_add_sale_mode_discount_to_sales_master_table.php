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
        Schema::table('sales_master', function (Blueprint $table) {
            $table->enum('sale_mode', ['Cash', 'Credit', 'UPI'])->default('Cash')->after('customer_name');
            $table->decimal('discount_amount', 14, 2)->default(0)->after('sale_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_master', function (Blueprint $table) {
            $table->dropColumn(['sale_mode', 'discount_amount']);
        });
    }
};
