<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class ClientAdd extends AbstractTemplateStructure
{
    public function base(): array
    {
        return [
            'name'        => 'Boas-vindas',
            'description' => 'Modelo de mensagem enviada no cadastrado de um cliente',
        ];
    }

    public function variables(): array
    {
        return [
            'website'     => 'Link do Site',
            'whmcs'       => 'Link do WHMCS',
            'companyname' => 'Nome da Empresa',
        ];
    }
}
