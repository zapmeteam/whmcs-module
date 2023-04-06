<?php

namespace ZapMe\Whmcs\Actions;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\Module\Template;
use ZapMe\Whmcs\Module\Configuration;

class Hooks
{
    protected mixed $hooks;

    public function __construct(string $hook)
    {
        $zapme    = (new ZapMeSdk())->toUrl(ZAPME_MODULE_API_URL);
        $module   = (new Configuration())->fromDto();
        $template = (new Template($hook))->fromDto();

        $class       = "ZapMe\\Whmcs\\Actions\\Hooks\\".$hook;
        $this->hooks = new $class($zapme, $module, $template);
    }

    public function dispatch(mixed $vars): void
    {
        $this->hooks->execute($vars);
    }
}
