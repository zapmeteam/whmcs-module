<?php

namespace ZapMe\Whmcs\Actions;

use Symfony\Component\HttpFoundation\Request;

class Actions
{
    protected Request $request;

    protected ?string $action = null;

    private const ACTIONS = [
        'configuration'   => 'editModuleConfigurations',
        'templates'       => 'editModuleTemplateConfigurations',
        'editrules'       => 'editModuleTemplateRulesConfigurations',
        'logs'            => 'editModuleLogsConfigurations',
        'manualmessage'   => 'sendManualMessage',
        'serviceready'    => 'sendServiceReadyMessage',
        'invoicereminder' => 'sendInvoiceReminderMessage',
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->action  = $request->get('action');
    }

    public function execute(): mixed
    {
        if (!$this->action) {
            return null;
        }

        $externals = [
            'serviceready',
            'invoicereminder',
        ];

        $method = self::ACTIONS[$this->action];

        if (
            ((($this->request->getMethod() === 'GET' && in_array($this->action, $externals))) || $this->action === 'manualmessage')
        ) {
            return (new ExternalActions())->{$method}($this->request);
        }

        return (new InternalActions())->{$method}($this->request);
    }
}
