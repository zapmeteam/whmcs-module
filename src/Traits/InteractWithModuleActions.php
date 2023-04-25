<?php

namespace ZapMe\Whmcs\Traits;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\Actions\CreateSdkInstance;

trait InteractWithModuleActions
{
    use InteractWithCarbon;

    public function sdk(): ZapMeSdk
    {
        return CreateSdkInstance::execute();
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
