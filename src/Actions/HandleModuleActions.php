<?php

namespace ZapMe\Whmcs\Actions;

use Symfony\Component\HttpFoundation\Request;

class HandleModuleActions
{
    protected Request $request;

    protected ?string $action = null;
    protected ?string $type   = null;

    private const ACTIONS = [
        'internal' => [
            'configuration' => 'editModuleConfigurations',
            'templates'     => 'editModuleTemplateConfigurations',
            'logs'          => 'editModuleLogsConfigurations',
        ],

        'external' => [
            'manualmessage'   => 'sendManualMessage',
            'serviceready'    => 'sendServiceReadyMessage',
            'invoicereminder' => 'sendInvoiceReminderMessage',
        ]
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->action  = $request->get('action');
        $this->type    = $request->get('type');
    }

    public function execute(): mixed
    {
        if (!$this->action || !$this->type) {
            return null;
        }

        $method = data_get(self::ACTIONS, "{$this->type}.{$this->action}");

        if ($this->type === 'internal') {
            return (new ExecuteModuleInternalActions())->{$method}($this->request);
        }

        return (new ExecuteModuleExternalActions())->{$method}($this->request);
    }
}
