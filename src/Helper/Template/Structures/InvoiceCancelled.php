<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class InvoiceCancelled extends AbstractTemplateStructure
{
    public function base(): array
    {
        return [
            'name'        => 'Fatura Cancelada',
            'description' => 'Modelo de mensagem enviada quando uma fatura é cancelada',
        ];
    }

    public function rules(): array
    {
        return (new InvoiceCreated())->rules();
    }

    public function variables(): array
    {
        return (new InvoicePaid())->variables();
    }
}
