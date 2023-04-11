<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class ClientChangePassword extends AbstractTemplateStructure
{
    public function base(): array
    {
        return [
            'name'        => 'Troca de Senha',
            'description' => 'Modelo de mensagem enviada na troca de senha da conta de um cliente',
        ];
    }

    public function variables(): array
    {
        return [];
    }
}
