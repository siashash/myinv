<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_name', 100)->unique();
            $table->string('short_name', 20)->unique();
        });

        DB::table('units')->insert([
            ['unit_name' => 'Kilogram', 'short_name' => 'KG'],
            ['unit_name' => 'Gram', 'short_name' => 'G'],
            ['unit_name' => 'Box', 'short_name' => 'BOX'],
            ['unit_name' => 'Piece', 'short_name' => 'PCS'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
