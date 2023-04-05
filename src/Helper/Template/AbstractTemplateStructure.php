<?php

namespace ZapMe\Whmcs\Helper\Template;

abstract class AbstractTemplateStructure
{
    public static function execute(): object
    {
        return (object) array_merge(
            (new static())->base(),
            ['rules'     => (new static())->rules()],
            ['variables' => (new static())->variables()],
        );
    }
}
