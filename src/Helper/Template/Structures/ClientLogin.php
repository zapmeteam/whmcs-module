<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\TemplateRule;
use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class ClientLogin extends AbstractTemplateStructure
{
    public function base(): array
    {
        return [
            'name'        => 'Login de Cliente',
            'description' => 'Modelo de mensagem enviada no login de uma conta de cliente',
        ];
    }

    public function rules(): array
    {
        return [
            TemplateRule::build('client'),
            TemplateRule::build('weekdays'),
            TemplateRule::build('status')
        ];
    }

    public function variables(): array
    {
        return [
            'ipaddr' => 'IP de Acesso',
            'date'   => 'Data do Registro',
            'hour'   => 'Hora do Registro',
        ];
    }
}
