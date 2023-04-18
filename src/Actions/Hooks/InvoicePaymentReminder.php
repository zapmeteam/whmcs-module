<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Helper\Hooks\AbstractHookStructure;

class InvoicePaymentReminder extends AbstractHookStructure
{
    public function execute(mixed $vars): bool
    {
        $invoice = Capsule::table('tblinvoices')
            ->where('id', $vars['invoiceid'])
            ->first();

        $this->client = $this->client($invoice->userid);

        $this->parse(['invoice' => $invoice]);

        $this->send();

        return true;
    }
}
