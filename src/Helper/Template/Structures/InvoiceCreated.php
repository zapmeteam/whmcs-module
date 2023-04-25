<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class InvoiceCreated extends AbstractTemplateStructure
{
    public function descriptions(): array
    {
        return [
            'name'        => 'Fatura Criada',
            'description' => 'Modelo de mensagem enviada quando uma fatura é criada pelo WHMCS',
            'paghiper'    => true,
        ];
    }

    public function variables(): array
    {
        return [
            'invoiceid'        => 'Id da fatura',
            'duedate'          => 'Vencimento da fatura',
            'value'            => 'Valor total',
            'paghiper_barcode' => 'Código de Barras do Boleto Bancário da PagHiper',
        ];
    }
}
