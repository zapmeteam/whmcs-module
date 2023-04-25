<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class TicketAdminReply extends AbstractTemplateStructure
{
    public function descriptions(): array
    {
        return [
            'name'        => 'Ticket Respondido',
            'description' => 'Modelo de mensagem enviada quando um ticket é respondido pela equipe',
        ];
    }

    public function variables(): array
    {
        return (new TicketOpen())->variables();
    }
}
