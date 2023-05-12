<?php

use WHMCS\User\Client;
use WHMCS\Database\Capsule;
use Illuminate\Support\Carbon;

if (!defined('WHMCS')) {
    die;
}

const ZAPME_MODULE_PATH         = __DIR__;
const ZAPME_MODULE_API_URL      = 'http://api.zapme.test';
const ZAPME_MODULE_ACTIVITY_LOG = true;

if (!function_exists('throwlable')) {
    function throwlable(Throwable $exception): void
    {
        if (!ZAPME_MODULE_ACTIVITY_LOG) {
            return;
        }

        $source = $exception->getFile();
        $file   = explode('zapme/whmcs', $source);
        $file   = $file[1] ?? $source;

        logActivity("[ZapMe] Erro: {$exception->getMessage()} | {$file}: {$exception->getLine()}");
    }
}

if (!function_exists('now')) {
    function now(): Carbon
    {
        return Carbon::now('America/Sao_Paulo');
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
        return optional(
            Capsule::table('tblpaymentgateways')
            ->select('value')
            ->where('gateway', '=', 'paghiper')
            ->where('setting', '=', 'visible')
            ->first()
           )->value === 'on';
    }
}

if (!function_exists('whmcs_version')) {
    function whmcs_version(): int
    {
        $value = Capsule::table('tblconfiguration')
            ->where('setting', '=', 'Version')
            ->first()
            ->value;

        return substr($value, 0,1);
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
