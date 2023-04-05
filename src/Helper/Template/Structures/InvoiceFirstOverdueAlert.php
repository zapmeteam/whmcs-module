<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\TemplateRule;
use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class InvoiceFirstOverdueAlert extends AbstractTemplateStructure
{
    public function base(): array
    {
        return [
            'name'        => 'Fatura em Atraso <b>[1]</b>',
            'description' => 'Modelo de mensagem enviada no primeiro aviso de fatura em atraso',
            'paghiper'    => true,
        ];
    }

    public function rules(): array
    {
        return [
            TemplateRule::build('client'),
            TemplateRule::build('value'),
            TemplateRule::build('weekdays'),
            TemplateRule::build('gateways'),
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
