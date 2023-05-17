<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class InvoicePaymentReminder extends HookExecutionStructure
{
    public function execute($vars): bool
    {
        $invoice = Capsule::table('tblinvoices')
            ->where('id', '=', $vars['invoiceid'])
            ->first();

        $this->client = $this->client($invoice->userid);

        $this->parse(['invoice' => $invoice]);
        $this->billet($invoice);

        $this->send();

        return true;
    }
}
