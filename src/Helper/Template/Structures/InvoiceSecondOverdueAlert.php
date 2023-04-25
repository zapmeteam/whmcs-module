<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class InvoiceSecondOverdueAlert extends AbstractTemplateStructure
{
    public function descriptions(): array
    {
        return [
            'name'        => 'Fatura em Atraso <b>[2]</b>',
            'description' => 'Modelo de mensagem enviada no segundo aviso de fatura em atraso',
            'paghiper'    => true,
        ];
    }

    public function variables(): array
    {
        return (new InvoiceFirstOverdueAlert())->variables();
    }
}
