<?php

namespace ZapMe\Whmcs\Traits;

use ZapMe\Whmcs\Module\Api;
use ZapMe\Whmcs\DTO\ConfigurationDto;
use ZapMe\Whmcs\Actions\Sdk\CreateApiInstance;

trait InteractWithModuleActions
{
    public function request(?ConfigurationDto $configuration = null): Api
    {
        return CreateApiInstance::execute($configuration);
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
