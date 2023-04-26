<?php

namespace ZapMe\Whmcs\Actions\Sdk;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\DTO\ConfigurationDto;

class CreateSdkInstance
{
    public static function execute(?ConfigurationDto $configuration = null): ZapMeSdk
    {
        $sdk = (new ZapMeSdk())->toUrl(ZAPME_MODULE_API_URL);

        if ($configuration && $configuration->configured) {
            $sdk->withApi($configuration->api)
                ->withSecret($configuration->secret);
        }

        return $sdk;
    }
}
