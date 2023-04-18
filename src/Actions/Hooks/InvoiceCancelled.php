<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Helper\Hooks\AbstractHookStructure;

class InvoiceCancelled extends AbstractHookStructure
{
    public function execute(mixed $vars): void
    {
        $invoice = Capsule::table('tblinvoices')
            ->where('id', $vars['invoiceid'])
            ->first();

        $this->client = $this->client($invoice->userid);

        $this->parse(['invoice' => $invoice]);

        $this->send();
    }
}
