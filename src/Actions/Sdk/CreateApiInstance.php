<?php

namespace ZapMe\Whmcs\Actions\Sdk;

use ZapMe\Whmcs\Module\Api;
use ZapMe\Whmcs\DTO\ConfigurationDto;

class CreateApiInstance
{
    public static function execute(?ConfigurationDto $configuration = null): Api
    {
        $api = (new Api())->toUrl($_ENV['ZAPME_MODULE_API_URL'] ?? 'https://api.zapme.com.br');

        if ($configuration && $configuration->configured) {
            $api->withApi($_ENV['ZAPME_MODULE_API_KEY'] ?? $configuration->api)
                ->withSecret($_ENV['ZAPME_MODULE_API_SECRET'] ?? $configuration->secret);
        }

        return $api;
    }
}
