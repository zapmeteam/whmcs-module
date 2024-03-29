<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class TicketOpen extends AbstractTemplateStructure
{
    public function descriptions(): array
    {
        return [
            'name'        => 'Ticket Criado',
            'description' => 'Modelo de mensagem enviada quando um ticket é aberto pelo cliente',
        ];
    }

    public function variables(): array
    {
        return [
            'id'          => 'Id do Ticket',
            'tid'         => 'Tid do Ticket',
            'title'       => 'Título do Ticket',
            'ticketdate'  => 'Data da Abertura do Ticket',
            'tickethour'  => 'Hora da Abertura do Ticket',
            'deppartment' => 'Departamento do Ticket',
        ];
    }
}
