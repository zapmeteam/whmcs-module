<?php

use WHMCS\Database\Capsule;

if (!defined('WHMCS')) {
    die;
}
const ZAPME_MODULE_PATH = __DIR__;

Dotenv\Dotenv::createImmutable(ZAPME_MODULE_PATH)->safeLoad();

if (!function_exists('throwlable')) {
    function throwlable(Throwable $exception): void
    {
        if ((bool)$_ENV['ZAPME_MODULE_ACTIVITY_LOG'] === false) {
            return;
        }

        $source = $exception->getFile();
        $file   = explode('zapme/whmcs', $source);
        $file   = $file[1] ?? $source;

        logActivity("[ZapMe] Erro: {$exception->getMessage()} | {$file}: {$exception->getLine()} | {$exception->getTraceAsString()}");
    }
}

if (!function_exists('format_number')) {
    function format_number(string $number): string
    {
        return number_format($number, 2, ',', '.');
    }
}

if (!function_exists('paghiper_active')) {
    function paghiper_active(): bool
    {
        return (Capsule::table('tblpaymentgateways')
            ->select('value')
            ->where('gateway', '=', 'paghiper')
            ->where('setting', '=', 'visible')
            ->first()->value ?? null) === 'on';
    }
}

if (!function_exists('whmcs_version')) {
    function whmcs_version(): int
    {
        $value = Capsule::table('tblconfiguration')
            ->where('setting', '=', 'Version')
            ->first()
            ->value;

        return substr($value, 0, 1);
    }
}

if (!function_exists('sanitize')) {
    function sanitize(string $phone): string
    {
        return trim(str_replace(['(', ')', ' ', '-', '.', '+'], '', $phone));
    }
}

if (!function_exists('selected')) {
    function selected(bool $boolean, string $text = 'selected'): string
    {
        return $boolean ? $text : '';
    }
}
