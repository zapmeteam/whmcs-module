<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class AfterModuleReady extends AbstractTemplateStructure
{
    public function base(): array
    {
        return [
            'name'        => 'Serviço Pronto',
            'description' => 'Modelo de mensagem utilizado no botão de serviço pronto (uso manual)',
        ];
    }

    public function variables(): array
    {
        return (new AfterModuleCreate())->variables();
    }
}
