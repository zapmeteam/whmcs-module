<?php

namespace ZapMe\Whmcs\Actions\Sdk;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\DTO\ConfigurationDto;

class CreateSdkInstance
{
    public static function execute(?ConfigurationDto $configuration = null): ZapMeSdk
    {
        $sdk = (new ZapMeSdk())->toUrl($_ENV['ZAPME_MODULE_API_URL'] ?? 'https://api.zapme.com.br');

        if ($configuration && $configuration->configured) {
            $sdk->withApi($_ENV['ZAPME_MODULE_API_KEY'] ?? $configuration->api)
                ->withSecret($_ENV['ZAPME_MODULE_API_SECRET'] ?? $configuration->secret);
        }

        return $sdk;
    }
}
