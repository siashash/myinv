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
        Schema::table('sales_details', function (Blueprint $table) {
            $table->string('unit_name', 20)->default('uom')->after('uom');
            $table->decimal('amount', 14, 2)->default(0)->after('rate');
            $table->decimal('cgst_percent', 5, 2)->default(0)->after('total');
            $table->decimal('cgst_amount', 14, 2)->default(0)->after('cgst_percent');
            $table->decimal('sgst_percent', 5, 2)->default(0)->after('cgst_amount');
            $table->decimal('sgst_amount', 14, 2)->default(0)->after('sgst_percent');
            $table->decimal('igst_percent', 5, 2)->default(0)->after('sgst_amount');
            $table->decimal('igst_amount', 14, 2)->default(0)->after('igst_percent');
            $table->decimal('gst_amount', 14, 2)->default(0)->after('igst_amount');
            $table->decimal('net_amount', 14, 2)->default(0)->after('gst_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_details', function (Blueprint $table) {
            $table->dropColumn([
                'unit_name',
                'amount',
                'cgst_percent',
                'cgst_amount',
                'sgst_percent',
                'sgst_amount',
                'igst_percent',
                'igst_amount',
                'gst_amount',
                'net_amount',
            ]);
        });
    }
};
