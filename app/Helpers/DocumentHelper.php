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
        $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        if ($x < 12)
            return " " . $angka[$x];
        elseif ($x < 20)
            return self::terbilang($x - 10) . " belas"; // Perhatikan self::
        elseif ($x < 100)
            return self::terbilang($x / 10) . " puluh" . self::terbilang($x % 10);
        elseif ($x < 200)
            return " seratus" . self::terbilang($x - 100);
        elseif ($x < 1000)
            return self::terbilang($x / 100) . " ratus" . self::terbilang($x % 100);
        elseif ($x < 2000)
            return " seribu" . self::terbilang($x - 1000);
        elseif ($x < 1000000)
            return self::terbilang($x / 1000) . " ribu" . self::terbilang($x % 1000);
        elseif ($x < 1000000000)
            return self::terbilang($x / 1000000) . " juta" . self::terbilang($x % 1000000);
        elseif ($x < 1000000000000)
            return self::terbilang($x / 1000000000) . " milyar" . self::terbilang($x % 1000000000);
        elseif ($x < 1000000000000000)
            return self::terbilang($x / 1000000000000) . " trilyun" . self::terbilang($x % 1000000000000);
        return "";
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
