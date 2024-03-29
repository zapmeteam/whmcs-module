<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class AfterModuleTerminate extends AbstractTemplateStructure
{
    public function descriptions(): array
    {
        return [
            'name'        => 'Serviço Cancelado',
            'description' => 'Modelo de mensagem enviada quando um serviço é cancelado',
        ];
    }

    public function variables(): array
    {
        return (new AfterModuleCreate())->variables();
    }
}
