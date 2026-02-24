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
        $this->addUniqueIfPossible('categories', 'category_name');
        $this->addUniqueIfPossible('sub_categories', 'sub_category_name');
        $this->addUniqueIfPossible('products', 'product_name');
        $this->addUniqueIfPossible('suppliers', 'supplier_name');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropUniqueIfExists('categories', 'category_name');
        $this->dropUniqueIfExists('sub_categories', 'sub_category_name');
        $this->dropUniqueIfExists('products', 'product_name');
        $this->dropUniqueIfExists('suppliers', 'supplier_name');
    }

    private function addUniqueIfPossible(string $table, string $column): void
    {
        $indexName = "{$table}_{$column}_unique";

        if ($this->hasUniqueIndex($table, $indexName)) {
            return;
        }

        $duplicateExists = DB::table($table)
            ->select($column, DB::raw('COUNT(*) as cnt'))
            ->groupBy($column)
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicateExists) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->unique($column);
        });
    }

    private function dropUniqueIfExists(string $table, string $column): void
    {
        $indexName = "{$table}_{$column}_unique";

        if (! $this->hasUniqueIndex($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->dropUnique([$column]);
        });
    }

    private function hasUniqueIndex(string $table, string $indexName): bool
    {
        $result = DB::selectOne(
            'SELECT COUNT(*) as count FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? AND non_unique = 0',
            [$table, $indexName]
        );

        return (int) ($result->count ?? 0) > 0;
    }
};
