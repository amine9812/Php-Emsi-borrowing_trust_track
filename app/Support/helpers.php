<?php
// Global helpers for date math, clamping, and common timestamps.

if (!function_exists('date_diff_days')) {
    function date_diff_days(string $a, string $b): int
    {
        $da = new DateTime($a);
        $db = new DateTime($b);
        $diff = $da->diff($db);
        $days = (int) $diff->days;

        return $da < $db ? -$days : $days;
    }
}

if (!function_exists('clamp_int')) {
    function clamp_int(int $value, int $min, int $max): int
    {
        return max($min, min($max, $value));
    }
}

if (!function_exists('today_date')) {
    function today_date(): string
    {
        return date('Y-m-d');
    }
}

if (!function_exists('now_iso')) {
    function now_iso(): string
    {
        return date('c');
    }
}
