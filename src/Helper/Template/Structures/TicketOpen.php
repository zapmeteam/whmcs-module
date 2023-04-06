<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\TemplateRule;
use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class TicketOpen extends AbstractTemplateStructure
{
    public function base(): array
    {
        return [
            'name'        => 'Ticket Criado',
            'description' => 'Modelo de mensagem enviada quando um ticket é aberto pelo cliente',
        ];
    }

    public function rules(): array
    {
        return [
            TemplateRule::get('client'),
            TemplateRule::get('weekdays'),
            TemplateRule::get('departments'),
        ];
    }

    public function variables(): array
    {
        return [
            'id'          => 'Id do Ticket',
            'tid'         => 'Tid do Ticket',
            'title'       => 'Título do Ticket',
            'date'        => 'Data da Abertura do Ticket',
            'hour'        => 'Hora da Abertura do Ticket',
            'deppartment' => 'Departamento do Ticket',
        ];
    }
}
