<?php

namespace ZapMe\Whmcs\Traits;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\DTO\ConfigurationDto;
use ZapMe\Whmcs\Actions\Sdk\CreateSdkInstance;

trait InteractWithModuleActions
{
    public function sdk(?ConfigurationDto $configuration = null): ZapMeSdk
    {
        return CreateSdkInstance::execute($configuration);
    }

    public function success(string $message): string
    {
        return "<div class=\"alert alert-success text-center\">
                    <i class=\"fa fa-check-circle\"></i>
                    {$message}
                </div>";
    }

    public function danger(string $message): string
    {
        return "<div class=\"alert alert-danger text-center\">
                    <i class=\"fa fa-exclamation-circle\"></i>
                    {$message}
                </div>";
    }
}
