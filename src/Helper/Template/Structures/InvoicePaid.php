<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class InvoicePaid extends AbstractTemplateStructure
{
    public function base(): array
    {
        return [
            'name'        => 'Fatura Paga',
            'description' => 'Modelo de mensagem enviada quando o pagamento de uma fatura é confirmado',
        ];
    }

    public function variables(): array
    {
        return [
            'invoiceid' => 'Id da Fatura',
            'duedate'   => 'Vencimento da Fatura',
            'value'     => 'Valor Total',
        ];
    }
}
