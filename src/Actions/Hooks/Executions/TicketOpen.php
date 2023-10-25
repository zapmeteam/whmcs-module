<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class TicketOpen extends HookExecutionStructure
{
    public function execute($vars): bool
    {
        $ticket = Capsule::table('tbltickets')
            ->where('id', '=', $vars['ticketid'])
            ->first();

        $this->client = $this->client($vars['userid']);

        $this->vars($vars);
        $this->parse(['ticket' => $ticket]);

        $this->send();

        return true;
    }
}
