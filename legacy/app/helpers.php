<?php
// Common helpers for escaping, redirects, flashes, and date math.

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash_set(string $type, string $message): void
{
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flash_get_all(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function date_diff_days(string $a, string $b): int
{
    $da = new DateTime($a);
    $db = new DateTime($b);
    $diff = $da->diff($db);
    $days = (int) $diff->days;

    return $da < $db ? -$days : $days;
}

function clamp_int(int $value, int $min, int $max): int
{
    return max($min, min($max, $value));
}

function today_date(): string
{
    return date('Y-m-d');
}

function now_iso(): string
{
    return date('c');
}
