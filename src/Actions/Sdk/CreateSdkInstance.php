<?php

namespace ZapMe\Whmcs\Actions\Sdk;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\DTO\ConfigurationDto;

class CreateSdkInstance
{
    public static function execute(?ConfigurationDto $configuration = null): ZapMeSdk
    {
        $sdk = (new ZapMeSdk())->toUrl(configuration('zapme_api_url'));

        if ($configuration && $configuration->configured) {
            $sdk->withApi(configuration('zapme_api_key', $configuration->api))
                ->withSecret(configuration('zapme_api_secret', $configuration->secret));
        }

        return $sdk;
    }
}
