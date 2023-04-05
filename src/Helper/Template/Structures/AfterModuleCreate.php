<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\TemplateRule;
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

    public function rules(): array
    {
        return [
            TemplateRule::build('client'),
            TemplateRule::build('weekdays'),
            TemplateRule::build('server'),
            TemplateRule::build('product'),
        ];
    }

    public function variables(): array
    {
        return [
            'product'  => 'Nome do serviço',
            'id'       => 'Id do serviço',
            'duedate'  => 'Data de vencimento (d/m/Y)',
            'value'    => 'Valor total',
            'ip'       => 'IP do serviço',
            'domain'   => 'Domínio do serviço',
            'user'     => 'Usuário do serviço',
            'password' => 'Senha do serviço',
        ];
    }
}
