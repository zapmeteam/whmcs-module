<?php

namespace ZapMe\Whmcs\Traits;

use ZapMe\Whmcs\Module\Request;
use ZapMe\Whmcs\DTO\ConfigurationDto;
use ZapMe\Whmcs\Actions\Sdk\CreateRequestInstance;

trait InteractWithModuleActions
{
    public function request(?ConfigurationDto $configuration = null): Request
    {
        return CreateRequestInstance::execute($configuration);
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
