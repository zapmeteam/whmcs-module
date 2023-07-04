<?php

namespace ZapMe\Whmcs\Actions\Sdk;

use ZapMe\Whmcs\Module\Request;
use ZapMe\Whmcs\DTO\ConfigurationDto;

class CreateRequestInstance
{
    public static function execute(?ConfigurationDto $configuration = null): Request
    {
        $api = (new Request())->toUrl($_ENV['ZAPME_MODULE_API_URL'] ?? 'https://api.zapme.com.br');

        if ($configuration && $configuration->configured) {
            $api->withApi($_ENV['ZAPME_MODULE_API_KEY'] ?? $configuration->api)
                ->withSecret($_ENV['ZAPME_MODULE_API_SECRET'] ?? $configuration->secret);
        }

        return $api;
    }
}
