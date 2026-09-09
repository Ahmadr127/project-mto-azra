<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Helper untuk default rentang tanggal dinamis - hanya untuk data MCU
 * Tidak pakai env/config, cukup ganti 1 nilai defaultRange di component/service.
 *
 * Contoh dinamis siap pakai (komentar):
 *  '7 days'   => 7 hari terakhir
 *  '14 days'  => 2 minggu
 *  '30 days'  => 1 bulan (default, ~30 hari) - REKOMENDASI DEFAULT
 *  '60 days'  => 2 bulan
 *  '90 days'  => 3 bulan
 *  '1 week'   => 1 minggu
 *  '2 weeks'  => 2 minggu
 *  '1 month'  => 1 bulan kalender (subMonth)
 *  '2 months' => 2 bulan kalender
 *  '3 months' => 3 bulan kalender
 *  '6 months' => 6 bulan
 *  '1 year'   => 1 tahun
 *  null / ''  => tanpa default (tampilkan semua)
 *
 * Semua view yang pakai <x-date-range-filter /> tanpa prop otomatis pakai '30 days'.
 * Override per-view: <x-date-range-filter default-range="60 days" />
 */
class DateRangeHelper
{
    /**
     * Parse rentang dinamis jadi [from, to] Y-m-d.
     * @param string|null $range contoh '30 days', '2 months', '1 year', null = tanpa default
     * @return array{0: string|null, 1: string|null} [from, to]
     */
    public static function parse(?string $range): array
    {
        if (!$range) {
            return [null, null];
        }

        $range = trim(strtolower($range));
        $now = Carbon::now();

        // Format: "30 days", "2 months", "1 year", "2 weeks"
        if (preg_match('/^(\d+)\s*(day|days|week|weeks|month|months|year|years)$/', $range, $m)) {
            $num = (int) $m[1];
            $unit = strtolower($m[2]);

            $from = match (true) {
                str_starts_with($unit, 'day') => $now->copy()->subDays($num),
                str_starts_with($unit, 'week') => $now->copy()->subWeeks($num),
                str_starts_with($unit, 'month') => $now->copy()->subMonths($num),
                str_starts_with($unit, 'year') => $now->copy()->subYears($num),
                default => $now->copy()->subDays($num),
            };

            return [$from->toDateString(), $now->toDateString()];
        }

        // Fallback: jika format tidak dikenali, anggap 30 days
        return [$now->copy()->subDays(30)->toDateString(), $now->toDateString()];
    }

    /**
     * Ambil default untuk request: jika filter_date_from/to sudah ada, hargai user;
     * jika kosong, kembalikan default dari $range.
     */
    public static function defaultsForRequest(?string $range = '30 days'): array
    {
        return self::parse($range);
    }
}
