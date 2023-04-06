<?php

namespace ZapMe\Whmcs\Helper\Hooks;

interface HookStructureInterface
{
    public function execute(mixed $vars): void;
}