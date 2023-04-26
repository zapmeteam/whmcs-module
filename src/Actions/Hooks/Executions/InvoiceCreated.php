<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class InvoiceCreated extends HookExecutionStructure
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
