<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class InvoiceSecondOverDueAlert extends HookExecutionStructure
{
    public function execute(mixed $vars): bool
    {
        $invoice = Capsule::table('tblinvoices')
            ->where('id', $vars['relid'])
            ->first();

        $this->client = $this->client($invoice->userid);

        $this->parse(['invoice' => $invoice]);
        $this->paghiper($invoice);

        $this->send();

        return true;
    }
}
