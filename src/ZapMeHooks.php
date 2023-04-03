<?php

namespace ZapMe\Whmcs;

use DateTime;
use ZapMeSdk\Base;
use WHMCS\User\Client;
use WHMCS\Service\Service;
use WHMCS\Database\Capsule;

if (!defined('WHMCS')) {
    die('Denied access');
}

class ZapMeHooks
{
    /** * @var object */
    private $module;

    /** * @var string|null */
    private $hook;

    /** * @var Base */
    private $base;

    public function __construct($zapMeModule = null)
    {
        $this->module = $zapMeModule ?? Capsule::table('mod_zapme')->first();
    }

    /**
     * Prepare to dispatch a hook function
     *
     * @param string $hook
     * 
     * @return ZapMeHooks
     */
    public function prepare(string $hook): ZapMeHooks
    {
        if ($this->hook !== null) {
            $this->hook = null;
        }

        $this->hook = $hook;

        if (isset($this->module->api) && isset($this->module->secret)) {
            $this->base = (new Base())
                ->withApi($this->module->api)
                ->withSecret($this->module->secret);
        }

        return $this;
    }

    /**
     * Dispatch a hook function
     *
     * @param mixed $vars
     * 
     * @return void
     */
    public function dispatch($vars)
    {
        if (!isset($this->module->id)) {
            if (ZAPME_MODULE_ACTIVITY_LOG === true) {
                logActivity('[ZapMe][' . $this->hook . '] Processo Abortado: Módulo não configurado');
            }
            return;
        }

        if ($this->module->status == 0) {
            if (ZAPME_MODULE_ACTIVITY_LOG === true) {
                logActivity('[ZapMe][' . $this->hook . '] Processo Abortado: Módulo desativado');
            }
            return;
        }

        $hook = $this->hook;

        return $this->$hook($vars);
    }

    /**
     * Get module configurations loaded
     *
     * @return object
     */
    public function getModuleConfiguration(): object
    {
        return $this->module;
    }

    /**
     * Invoice Created Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function InvoiceCreated($vars)
    {
        $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
        $client  = Client::find($invoice->userid);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByMinimalValue($invoice) === false ||
                $template->controlByWeekDay() === false
            ) {
                return;
            }
        }

        $template->defaultVariables($client)->invoicesVariables($invoice);
        $document = $template->attachPagHiperBilletOnHooks($invoice, $client);

        $message = $template->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id, $document);
    }

    /**
     * Invoice Created Hook
     *
     * @param mixed $vars
     *
     * @return void
     */
    private function InvoiceCancelled($vars)
    {
        $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
        $client  = Client::find($invoice->userid);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByMinimalValue($invoice) === false ||
                $template->controlByWeekDay() === false
            ) {
                return;
            }
        }

        $template->defaultVariables($client)->invoicesVariables($invoice);
        $message = $template->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id);
    }

    /**
     * Invoice Payment Reminder Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    public function InvoicePaymentReminder($vars, bool $externalAction = false)
    {
        $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
        $client  = Client::find($invoice->userid);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByMinimalValue($invoice) === false ||
                $template->controlByWeekDay() === false ||
                $template->controlByGateway($invoice) === false
            ) {
                return;
            }
        }

        $template->defaultVariables($client)->invoicesVariables($invoice);
        $document = $template->attachPagHiperBilletOnHooks($invoice, $client);

        $message = $template->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id, $document);

        if ($externalAction) {
            return true;
        }
    }

    /**
     * Invoice Paid Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function InvoicePaid($vars)
    {
        $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
        $client  = Client::find($invoice->userid);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByMinimalValue($invoice) === false ||
                $template->controlByWeekDay() === false ||
                $template->controlByGateway($invoice) === false
            ) {
                return;
            }
        }

        $message = $template->defaultVariables($client)->invoicesVariables($invoice)->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id);
    }

    /**
     * Invoice First Overdue Alert Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function InvoiceFirstOverDueAlert($vars)
    {
        $invoice = Capsule::table('tblinvoices')->where('id', $vars['relid'])->first();
        $client  = Client::find($invoice->userid);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByMinimalValue($invoice) === false ||
                $template->controlByWeekDay() === false
            ) {
                return;
            }
        }

        $template->defaultVariables($client)->invoicesVariables($invoice);
        $document = $template->attachPagHiperBilletOnHooks($invoice, $client);

        $message = $template->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id, $document);
    }

    /**
     * Invoice Second Overdue Alert Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function InvoiceSecondOverDueAlert($vars)
    {
        $invoice = Capsule::table('tblinvoices')->where('id', $vars['relid'])->first();
        $client  = Client::find($invoice->userid);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByMinimalValue($invoice) === false ||
                $template->controlByWeekDay() === false
            ) {
                return;
            }
        }

        $template->defaultVariables($client)->invoicesVariables($invoice);
        $document = $template->attachPagHiperBilletOnHooks($invoice, $client);

        $message = $template->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id, $document);
    }

    /**
     * Invoice Third Overdue Alert Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function InvoiceThirdOverDueAlert($vars)
    {
        $invoice = Capsule::table('tblinvoices')->where('id', $vars['relid'])->first();
        $client  = Client::find($invoice->userid);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByMinimalValue($invoice) === false ||
                $template->controlByWeekDay() === false
            ) {
                return;
            }
        }

        $template->defaultVariables($client)->invoicesVariables($invoice);
        $document = $template->attachPagHiperBilletOnHooks($invoice, $client);

        $message = $template->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id, $document);
    }

    /**
     * Ticket Open Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function TicketOpen($vars)
    {
        $ticket = Capsule::table('tbltickets')->where('id', $vars['ticketid'])->first();
        $client = Client::find($vars['userid']);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByWeekDay() === false ||
                $template->controlByDeppartment($vars) === false
            ) {
                return;
            }
        }

        $message = $template->defaultVariables($client)->ticketsVariables($ticket, $vars)->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id);
    }

    /**
     * Ticket Admin Reply Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function TicketAdminReply($vars)
    {
        $ticket = Capsule::table('tbltickets')->where('id', $vars['ticketid'])->first();
        $client = Client::find($ticket->userid);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByWeekDay() === false ||
                $template->controlByDeppartment($vars) === false ||
                $template->controlByTeamMemberName($vars) === false
            ) {
                return;
            }
        }

        $message = $template->defaultVariables($client)->ticketsVariables($ticket, $vars)->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id);
    }

    /**
     * After Module Create Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function AfterModuleCreate($vars)
    {
        $service = Service::find($vars['params']['serviceid']);
        $client  = $service['client'];
        $product = $service['product'];

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByWeekDay() === false ||
                $template->controlByServerId($service) === false ||
                $template->controlByProductId($product) === false ||
                $template->controlByPartsOfProductName($product) === false
            ) {
                return;
            }
        }

        $message = $template->defaultVariables($client)->serviceVariables($service, $product)->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id);
    }

    /**
     * After Module Suspend Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function AfterModuleSuspend($vars)
    {
        $service = Service::find($vars['params']['serviceid']);
        $client  = $service['client'];
        $product = $service['product'];

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByWeekDay() === false ||
                $template->controlByServerId($service) === false ||
                $template->controlByProductId($product) === false ||
                $template->controlByPartsOfProductName($product) === false
            ) {
                return;
            }
        }

        $message = $template->defaultVariables($client)->serviceVariables($service, $product)->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id);
    }

    /**
     * After Module Unsuspend Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function AfterModuleUnsuspend($vars)
    {
        $service = Service::find($vars['params']['serviceid']);
        $client  = $service['client'];
        $product = $service['product'];

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByWeekDay() === false ||
                $template->controlByServerId($service) === false ||
                $template->controlByProductId($product) === false ||
                $template->controlByPartsOfProductName($product) === false
            ) {
                return;
            }
        }

        $message = $template->defaultVariables($client)->serviceVariables($service, $product)->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id);
    }

    /**
     * After Module Terminate Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function AfterModuleTerminate($vars)
    {
        $service = Service::find($vars['params']['serviceid']);
        $client  = $service['client'];
        $product = $service['product'];

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByWeekDay() === false ||
                $template->controlByServerId($service) === false ||
                $template->controlByProductId($product) === false ||
                $template->controlByPartsOfProductName($product) === false
            ) {
                return;
            }
        }

        $message = $template->defaultVariables($client)->serviceVariables($service, $product)->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id);
    }

    /**
     * Client Add Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function ClientAdd($vars)
    {
        $client = Client::find($vars['userid']);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByPartsOfEmail($client) === false ||
                $template->controlByWeekDay() === false
            ) {
                return;
            }
        }

        $message = $template->defaultVariables($client)->clientVariables($client, $vars, 'register')->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id);
    }

    /**
     * Client Login Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function ClientLogin($vars)
    {
        $client = Client::find($vars['userid']);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByPartsOfEmail($client) === false ||
                $template->controlByWeekDay() === false ||
                $template->controlByClientStatus($client) === false
            ) {
                return;
            }
        }

        $message = $template->defaultVariables($client)->clientVariables($client, $vars)->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id);
    }

    /**
     * Client Area Page Login Hook (Failed Access)
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function ClientAreaPageLogin($vars)
    {
        $log  = Capsule::table('tblactivitylog')->where('user', 'System')->orderBy('date', 'desc')->first();
        $text = explode(" ", $log->description);

        if ($text[0] !== 'Failed' && $text[1] !== 'Login' && $text[2] !== 'Attempt') {
            return;
        }

        $client = Client::find($log->userid);

        if (!isset($client->id)) {
            return;
        }

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByPartsOfEmail($client) === false ||
                $template->controlByWeekDay() === false ||
                $template->controlByClientStatus($client) === false
            ) {
                return;
            }
        }

        $message = $template->defaultVariables($client)->clientVariables($client, $vars)->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $logDate     = new DateTime($log->date);
        $currentDate = new DateTime;
        $dateDiff    = $logDate->diff($currentDate);

        if ($dateDiff->d == 0 && $dateDiff->i == 0 && ($dateDiff->s < 2)) {
            $this->endOfDispatch($message, $phone, $client->id);
        }
    }

    /**
     * Client Change Password Hook
     *
     * @param mixed $vars
     * 
     * @return void
     */
    private function ClientChangePassword($vars)
    {
        $client = Client::find($vars['userid']);

        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
            return;
        }

        $template = new ZapMeTemplateHandle($this->hook);

        if ($template->templateStatus() === false) {
            return;
        }

        if ($template->templateHasValidConfigurations() === true) {
            if (
                $template->controlByClient($client) === false ||
                $template->controlByPartsOfEmail($client) === false ||
                $template->controlByWeekDay() === false ||
                $template->controlByClientStatus($client) === false
            ) {
                return;
            }
        }

        $message = $template->defaultVariables($client)->clientVariables($client, $vars)->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);

        $this->endOfDispatch($message, $phone, $client->id);
    }

    /**
     * Daily Cron Job Hook
     *
     * @param [type] $vars
     * @return void
     */
    private function DailyCronJob($vars)
    {
        $date = (int) date('d');

        if ($date == 1 && $this->module->logautoremove == 1) {
            if (ZAPME_MODULE_ACTIVITY_LOG === true) {
                logActivity('[ZapMe][' . $this->hook . '] Rotina de Limpeza de Registros de Logs');
            }
            Capsule::table('mod_zapme_logs')->truncate();
        }

        $result = $this->base->accountStatus();

        if (!isset($result['result']) || $result['result'] !== 'success') {
            return;
        }

        $service = serialize([
            'status'  => $result['data']['service']['status'],
            'duedate' => $result['data']['service']['duedate'],
            'plan'    => $result['data']['service']['plan'],
            'auth'    => $result['data']['auth']['status'],
        ]);

        Capsule::table('mod_zapme')->where('id', 1)->update(['service' => $service]);

        if (ZAPME_MODULE_ACTIVITY_LOG === true) {
            logActivity('[ZapMe][' . $this->hook . '] Atualização dos Dados do Serviço da ZapMe');
        }
    }

    /**
     * End of dispatch (fire sendMessage from ZapMeApi)
     *
     * @param string $message
     * @param string $phone
     * @param integer $clientId
     * @param array $document
     *
     * @return void
     */
    private function endOfDispatch(string $message, string $phone, int $clientId, array $document = []): void
    {
        $response = $this->base->sendMessage($phone, $message, $document);

        if (isset($response['result']) && $response['result'] === 'created') {
            if (ZAPME_MODULE_ACTIVITY_LOG === true) {
                logActivity('[ZapMe][' . $this->hook . '] Envio de Mensagem: Sucesso.');
            }

            if ($this->module->logsystem == 1) {
                moduleSaveLog($message, strtolower($this->hook), $clientId);
            }

            return;
        }

        if (ZAPME_MODULE_ACTIVITY_LOG === true) {
            logActivity('[ZapMe][' . $this->hook . '] Envio de Mensagem: Erro: ' . $response['result']);
        }
    }
}
