<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SearchHelper
{
    /**
     * Case-insensitive LIKE untuk Postgres (ILIKE) dan SQLite/MySQL (LOWER).
     * Semua filter seharusnya tidak case sensitive.
     */
    public static function whereLike(Builder $query, string $column, string $value, string $boolean = 'and'): Builder
    {
        $value = '%'.strtolower($value).'%';
        // Gunakan LOWER() agar jalan di pgsql (production) dan sqlite (tests) + mysql
        // Kolom bisa "table.column" -> lower tiap part
        $lowerColumn = self::lowerColumn($column);

        return $boolean === 'or'
            ? $query->orWhereRaw("LOWER({$lowerColumn}) LIKE ?", [$value])
            : $query->whereRaw("LOWER({$lowerColumn}) LIKE ?", [$value]);
    }

    /**
     * Helper untuk closure whereHas: where column ILIKE value
     */
    public static function orWhereLike(Builder $query, string $column, string $value): Builder
    {
        return self::whereLike($query, $column, $value, 'or');
    }

    private static function lowerColumn(string $column): string
    {
        return $column;
    }

    /**
     * Untuk search global multi-kolom: where (col1 ILIKE or col2 ILIKE ...)
     * @param string[] $columns
     */
    public static function whereLikeAny(Builder $query, array $columns, string $value): void
    {
        $query->where(function (Builder $q) use ($columns, $value) {
            foreach ($columns as $i => $col) {
                $boolean = $i === 0 ? 'and' : 'or';
                self::whereLike($q, $col, $value, $boolean);
            }
        });
    }
}
