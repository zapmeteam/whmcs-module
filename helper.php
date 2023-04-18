<?php

use WHMCS\User\Client;
use WHMCS\Database\Capsule;

if (!defined('WHMCS')) {
    die;
}

const ZAPME_MODULE_PATH         = __DIR__;
const ZAPME_MODULE_ACTIVITY_LOG = true;
const ZAPME_MODULE_API_URL      = 'http://api.zapme.test';

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

if (!function_exists('modulePagHiperExtractPdf')) {
    function modulePagHiperExtractPdf(string $link): string
    {
        $billet = base64_encode(file_get_contents($link));

        if ($billet === null || empty($billet)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $link);
            $return = curl_exec($ch);
            curl_close($ch);

            $billet = base64_encode($return);
        }

        return $billet;
    }
}

if (!function_exists('modulePagHiperExist')) {
    function modulePagHiperExist(): bool
    {
        $modulePagHiper = Capsule::table('tblpaymentgateways')->where('gateway', 'paghiper')->where('setting', 'visible')->first();

        return $modulePagHiper->value === 'on';
    }
}

if (!function_exists('moduleSaveLog')) {
    /**
     * Save module logs
     *
     * @param string $message
     * @param string $code
     * @param integer $clientId
     *
     * @return boolean
     */
    function moduleSaveLog(string $message, string $code, int $clientId): bool
    {
        $now = date('Y-m-d H:i:s');

        Capsule::table('mod_zapme_logs')->insert([
            'code'       => $code,
            'message'    => $message,
            'clientid'   => $clientId,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        return true;
    }
}

if (!function_exists('clientConsentiment')) {
    function clientConsentiment(string $hook, $client, int $clientConsentFieldId): bool
    {
        if (!isset($client->id)) {
            logActivity('[ZapMe][' . $hook . '] Processo Abortado: A captura de dados do cliente não obteve resultados suficientes.');
            return false;
        }

        if ($clientConsentFieldId == 0) {
            return true;
        }

        if (empty($client->customFieldValues)) {
            return true;
        }

        foreach ($client->customFieldValues as $key => $field) {
            if ((int) $field->fieldid == (int) $clientConsentFieldId) {
                $value = $field->value;
            }
        }

        if ($value === null) {
            return true;
        }

        $value = str_replace(['Ã', 'ã'], 'a', $value);
        $value = strtolower($value);

        if ($value === 'n' || $value === 'nao') {
            if (ZAPME_MODULE_ACTIVITY_LOG === true) {
                logActivity('[ZapMe][' . $hook . '] Envio de Mensagem Abortado: O cliente ' . $client->firstname . ' (#' . $client->id . ') desativou o recebimento de alertas através do campo customizado');
            }
            return false;
        }

        return true;
    }
}

if (!function_exists('clientPhoneNumber')) {
    function clientPhoneNumber($client, int $clientPhoneFieldId): string
    {
        if ($clientPhoneFieldId == 0) {
            return trim(str_replace(['(', ')', ' ', '-', '.', '+'], '', $client->phonenumber));
        }

        $value = '';

        foreach ($client->customFieldValues as $field) {
            if ((int) $field->fieldid == $clientPhoneFieldId) {
                $value = $field->value;
            }
        }

        $value = trim(str_replace(['(', ')', ' ', '-', '.', '+'], '', $value));

        $phone = explode('.', $client->phonenumber);
        $ddi   = str_replace(['+', ' '], '', $phone[0]);
        $value = $ddi . $value;

        return $value;
    }
}

if (!function_exists('format_number')) {
    function format_number(string $number): string
    {
        return number_format($number, 2, ',', '.');
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
