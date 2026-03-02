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
        Schema::table('role_permission', function (Blueprint $table) {
            $table->boolean('can_add')->default(false)->after('permission_id');
            $table->boolean('can_edit')->default(false)->after('can_add');
            $table->boolean('can_delete')->default(false)->after('can_edit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_permission', function (Blueprint $table) {
            $table->dropColumn(['can_add', 'can_edit', 'can_delete']);
        });
    }
};
