<?php

namespace App\Helpers;

use Carbon\Carbon;

class DocumentHelper
{
    public static function generatePKDNumber($sequence, $companyName, $date = null)
    {
        $date = $date ? Carbon::parse($date) : now();
        $year = $date->format('Y');
        $month = self::getRomanMonth($date->format('m'));

        $initials = self::getInitials($companyName);

        return sprintf("%03d/PKD-%s/%s/%s", $sequence, $initials, $month, $year);
    }

    public static function terbilang($x) {
        // Normalize input and use integer arithmetic for stable recursion
        $x = (int) $x;

        $angka = [
            0 => '', 1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat', 5 => 'lima',
            6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan', 10 => 'sepuluh', 11 => 'sebelas'
        ];

        if ($x === 0) {
            return ' nol';
        }

        if ($x < 12) {
            return ' ' . ($angka[$x] ?? '');
        }

        if ($x < 20) {
            return self::terbilang($x - 10) . ' belas';
        }

        if ($x < 100) {
            $tens = intdiv($x, 10);
            $rest = $x % 10;
            $res = self::terbilang($tens) . ' puluh';
            if ($rest) $res .= self::terbilang($rest);
            return $res;
        }

        if ($x < 200) {
            return ' seratus' . self::terbilang($x - 100);
        }

        if ($x < 1000) {
            $hundreds = intdiv($x, 100);
            $rest = $x % 100;
            $res = self::terbilang($hundreds) . ' ratus';
            if ($rest) $res .= self::terbilang($rest);
            return $res;
        }

        if ($x < 2000) {
            return ' seribu' . self::terbilang($x - 1000);
        }

        // Larger units
        $units = [
            1000000000000 => ' trilyun',
            1000000000 => ' milyar',
            1000000 => ' juta',
            1000 => ' ribu'
        ];

        foreach ($units as $div => $name) {
            if ($x >= $div) {
                $count = intdiv($x, $div);
                $rest = $x % $div;
                $res = self::terbilang($count) . $name;
                if ($rest) $res .= self::terbilang($rest);
                return $res;
            }
        }

        return '';
    }

    private static function getInitials($string)
    {
        $words = explode(" ", strtoupper($string));
        $acronym = "";
        foreach ($words as $w) {
            $cleanWord = preg_replace('/[^A-Z]/', '', $w);
            if (!empty($cleanWord)) {
                $acronym .= $cleanWord[0];
            }
        }
        return $acronym;
    }

    private static function getRomanMonth($month)
    {
        $map = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V', '06' => 'VI',
            '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
        return $map[$month] ?? 'I';
    }
}
