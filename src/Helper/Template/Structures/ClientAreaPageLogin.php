<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class ClientAreaPageLogin extends AbstractTemplateStructure
{
    public function base(): array
    {
        return [
            'name'        => 'Falha de Login',
            'description' => 'Modelo de mensagem enviada na falha de login de uma conta de cliente',
        ];
    }

    public function variables(): array
    {
        return [];
    }
}
