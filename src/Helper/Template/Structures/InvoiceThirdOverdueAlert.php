<?php

namespace ZapMe\Whmcs\Helper\Template\Structures;

use ZapMe\Whmcs\Helper\Template\AbstractTemplateStructure;

class InvoiceThirdOverdueAlert extends AbstractTemplateStructure
{
    public function descriptions(): array
    {
        return [
            'name'        => 'Fatura em Atraso <b>[3]</b>',
            'description' => 'Modelo de mensagem enviada no terceiro aviso de fatura em atraso',
            'paghiper'    => true,
        ];
    }

    public function variables(): array
    {
        return (new InvoiceFirstOverdueAlert())->variables();
    }
}
