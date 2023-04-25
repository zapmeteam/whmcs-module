<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class AfterModuleSuspend extends AbstractTemplateStructure
{
    public function descriptions(): array
    {
        return [
            'name'        => 'Serviço Suspenso',
            'description' => 'Modelo de mensagem enviada quando um serviço é suspenso',
        ];
    }

    public function variables(): array
    {
        return (new AfterModuleCreate())->variables();
    }
}
