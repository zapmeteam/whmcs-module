<?php

namespace ZapMe\Whmcs\Actions;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\Module\Template;
use ZapMe\Whmcs\Module\Configuration;

class Hooks
{
    protected mixed $hooks;

    public function __construct(string $name)
    {
        $zapme    = (new ZapMeSdk())->toUrl(ZAPME_MODULE_API_URL);
        $module   = (new Configuration())->fromDto();
        $template = (new Template($name))->fromDto()->first();

        $class       = "ZapMe\\Whmcs\\Actions\\Hooks\\" . $name;
        $this->hooks = new $class($zapme, $module, $template);
    }

    public function dispatch(mixed $vars): void
    {
        $this->hooks::execute($vars);
    }
}
