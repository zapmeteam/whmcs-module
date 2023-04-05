<?php

namespace ZapMe\Whmcs\Helper\Template;

abstract class AbstractTemplateStructure
{
    public static function execute(): object
    {
        return (object) array_merge(
            (new static())->base(),
            (new static())->rules(),
            (new static())->variables(),
        );
    }
}
