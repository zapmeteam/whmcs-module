<?php

namespace ZapMe\Whmcs\Actions\Module;

use Symfony\Component\HttpFoundation\Request;

class HandleActions
{
    /** @var Request */
    protected $request;

    /** @var string|null */
    protected $action = null;

    /** @var string|null */
    protected $type = null;

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

    public function execute(): ?string
    {
        if (!$this->action || !$this->type) {
            return null;
        }

        $method = data_get(self::ACTIONS, "{$this->type}.{$this->action}");
        $class  = $this->type === 'internal' ? new InternalActions() : new ExternalActions();

        return $class->{$method}($this->request);
    }
}
