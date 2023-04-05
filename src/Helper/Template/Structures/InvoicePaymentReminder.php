<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class InvoicePaymentReminder extends AbstractTemplateStructure
{
    public function base(): array
    {
        return [
            'name'        => 'Lembrete de Fatura',
            'description' => 'Modelo de mensagem utilizado no lembrete de fatura em aberto (manualmente ou via hook)',
            'paghiper'    => true,
        ];
    }

    public function rules(): array
    {
        return (new InvoiceCreated())->rules();
    }

    public function variables(): array
    {
        return (new InvoiceCreated())->variables();
    }
}
