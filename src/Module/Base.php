<?php

namespace ZapMe\Whmcs\Module;

use ZapMeSdk\Base as ZapMeSdk;
use Illuminate\Support\Carbon;
use ZapMe\Whmcs\Traits\InteractWithCarbon;

class Base
{
    use InteractWithCarbon;

    protected ZapMeSdk $zapme;

    protected Carbon $carbon;

    public function __construct()
    {
        $this->zapme = (new ZapMeSdk())->toUrl(ZAPME_MODULE_API_URL);

        $this->carbonInstance();
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
