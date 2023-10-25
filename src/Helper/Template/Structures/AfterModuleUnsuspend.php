<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class AfterModuleUnsuspend extends AbstractTemplateStructure
{
    public function descriptions(): array
    {
        return [
            'name'        => 'Serviço Reativado',
            'description' => 'Modelo de mensagem enviada quando um serviço é reativado',
        ];
    }

    public function variables(): array
    {
        return (new AfterModuleCreate())->variables();
    }
}
