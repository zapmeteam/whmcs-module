<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class AfterModuleCreate extends AbstractTemplateStructure
{
    public function base(): array
    {
        return [
            'name'        => 'Serviço Criado',
            'description' => 'Modelo de mensagem enviada quando um serviço é criado',
        ];
    }

    public function variables(): array
    {
        return [
            'product'  => 'Nome do Serviço',
            'id'       => 'Id do Serviço',
            'duedate'  => 'Data de Vencimento',
            'value'    => 'Valor Total',
            'ip'       => 'IP do Serviço',
            'domain'   => 'Domínio do Serviço',
            'user'     => 'Usuário do Serviço',
            'password' => 'Senha do Serviço',
        ];
    }
}
