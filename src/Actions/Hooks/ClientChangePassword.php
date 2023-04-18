<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use ZapMe\Whmcs\Helper\Hooks\AbstractHookStructure;

class ClientChangePassword extends AbstractHookStructure
{
    public function execute(mixed $vars): void
    {
        $this->client = $this->client($vars['userid']);

        $this->send();
    }
}
