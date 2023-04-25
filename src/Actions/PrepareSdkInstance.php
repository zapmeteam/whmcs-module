<?php

namespace ZapMe\Whmcs\Actions;

use ZapMeSdk\Base as ZapMeSdk;

class PrepareSdkInstance
{
    public static function execute(): ZapMeSdk
    {
        return (new ZapMeSdk())->toUrl(ZAPME_MODULE_API_URL);
    }
}
