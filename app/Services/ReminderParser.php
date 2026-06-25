<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;

class ReminderParser
{
    private const MONTHS = [
        'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
        'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
        'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
    ];

    private const DAYS = [
        'senin' => Carbon::MONDAY, 'selasa' => Carbon::TUESDAY,
        'rabu' => Carbon::WEDNESDAY, 'kamis' => Carbon::THURSDAY,
        'jumat' => Carbon::FRIDAY, 'sabtu' => Carbon::SATURDAY,
        'minggu' => Carbon::SUNDAY,
    ];

    public function parse(string $input): ?Carbon
    {
        $input = strtolower(trim($input));

        return $this->parseHariIni($input)
            ?? $this->parseBesok($input)
            ?? $this->parseNHariLagi($input)
            ?? $this->parseNMingguLagi($input)
            ?? $this->parseNBulanLagi($input)
            ?? $this->parseTanggalSpesifik($input)
            ?? $this->parseSetiapHari($input)
            ?? $this->parseSetiapTanggal($input);
    }

    private function parseHariIni(string $input): ?Carbon
    {
        if (!preg_match('/^hari ini[,\s]+(.+)$/i', $input, $m)) {
            return null;
        }

        $time = $this->parseTime($m[1]);
        if (!$time) return null;

        return Carbon::today('Asia/Jakarta')->setTime($time['hour'], $time['minute'])->utc();
    }

    private function parseBesok(string $input): ?Carbon
    {
        if (!preg_match('/^besok[,\s]+(.+)$/i', $input, $m)) {
            return null;
        }

        $time = $this->parseTime($m[1]);
        if (!$time) return null;

        return Carbon::tomorrow('Asia/Jakarta')->setTime($time['hour'], $time['minute'])->utc();
    }

    private function parseNHariLagi(string $input): ?Carbon
    {
        if (!preg_match('/^(\d+)\s+hari lagi[,\s]+(.+)$/i', $input, $m)) {
            return null;
        }

        $time = $this->parseTime($m[2]);
        if (!$time) return null;

        return Carbon::now('Asia/Jakarta')->addDays((int)$m[1])->setTime($time['hour'], $time['minute'])->utc();
    }

    private function parseNMingguLagi(string $input): ?Carbon
    {
        if (!preg_match('/^(\d+)\s+minggu lagi[,\s]+(.+)$/i', $input, $m)) {
            return null;
        }

        $time = $this->parseTime($m[2]);
        if (!$time) return null;

        return Carbon::now('Asia/Jakarta')->addWeeks((int)$m[1])->setTime($time['hour'], $time['minute'])->utc();
    }

    private function parseNBulanLagi(string $input): ?Carbon
    {
        if (!preg_match('/^(\d+)\s+bulan lagi[,\s]+(.+)$/i', $input, $m)) {
            return null;
        }

        $time = $this->parseTime($m[2]);
        if (!$time) return null;

        return Carbon::now('Asia/Jakarta')->addMonths((int)$m[1])->setTime($time['hour'], $time['minute'])->utc();
    }

    private function parseTanggalSpesifik(string $input): ?Carbon
    {
        // Format: "25 juli, 7PM" or "25/07, 19:00"
        if (preg_match('/^(\d{1,2})\s+([a-z]+)[,\s]+(.+)$/i', $input, $m)) {
            $month = self::MONTHS[strtolower($m[2])] ?? null;
            if (!$month) return null;

            $time = $this->parseTime($m[3]);
            if (!$time) return null;

            $date = Carbon::createFromDate(now()->year, $month, (int)$m[1], 'Asia/Jakarta')
                ->setTime($time['hour'], $time['minute']);

            // Jika sudah lewat, pakai tahun depan
            if ($date->isPast()) {
                $date->addYear();
            }

            return $date->utc();
        }

        // Format: "25/07, 19:00"
        if (preg_match('/^(\d{1,2})\/(\d{1,2})[,\s]+(.+)$/', $input, $m)) {
            $time = $this->parseTime($m[3]);
            if (!$time) return null;

            $date = Carbon::createFromDate(now()->year, (int)$m[2], (int)$m[1], 'Asia/Jakarta')
                ->setTime($time['hour'], $time['minute']);

            if ($date->isPast()) {
                $date->addYear();
            }

            return $date->utc();
        }

        return null;
    }

    private function parseSetiapHari(string $input): ?Carbon
    {
        if (!preg_match('/^setiap\s+([a-z]+)[,\s]+(.+)$/i', $input, $m)) {
            return null;
        }

        $dayName = strtolower($m[1]);
        $dayOfWeek = self::DAYS[$dayName] ?? null;
        if ($dayOfWeek === null) return null;

        $time = $this->parseTime($m[2]);
        if (!$time) return null;

        $next = Carbon::now('Asia/Jakarta')->next($dayOfWeek)->setTime($time['hour'], $time['minute']);

        // Jika hari ini sama dan waktu belum lewat
        if (Carbon::now('Asia/Jakarta')->dayOfWeek === $dayOfWeek) {
            $today = Carbon::today('Asia/Jakarta')->setTime($time['hour'], $time['minute']);
            if ($today->isFuture()) {
                $next = $today;
            }
        }

        return $next->utc();
    }

    private function parseSetiapTanggal(string $input): ?Carbon
    {
        if (!preg_match('/^setiap tanggal\s+(\d{1,2})[,\s]+(.+)$/i', $input, $m)) {
            return null;
        }

        $time = $this->parseTime($m[2]);
        if (!$time) return null;

        $day = (int)$m[1];
        $now = Carbon::now('Asia/Jakarta');
        $next = Carbon::createFromDate($now->year, $now->month, $day, 'Asia/Jakarta')
            ->setTime($time['hour'], $time['minute']);

        if ($next->isPast()) {
            $next->addMonth();
        }

        return $next->utc();
    }

    private function parseTime(string $timeStr): ?array
    {
        $timeStr = trim($timeStr);

        // 24H: "09:00", "15:30"
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $timeStr, $m)) {
            return ['hour' => (int)$m[1], 'minute' => (int)$m[2]];
        }

        // 12H: "9AM", "3:30PM", "12PM"
        if (preg_match('/^(\d{1,2})(?::(\d{2}))?\s*(am|pm)$/i', $timeStr, $m)) {
            $hour = (int)$m[1];
            $minute = isset($m[2]) && $m[2] !== '' ? (int)$m[2] : 0;
            $period = strtolower($m[3]);

            if ($period === 'pm' && $hour !== 12) $hour += 12;
            if ($period === 'am' && $hour === 12) $hour = 0;

            return ['hour' => $hour, 'minute' => $minute];
        }

        return null;
    }
}
