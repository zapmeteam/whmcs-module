<?php

namespace ZapMe\Whmcs;

use WHMCS\User\Client;
use WHMCS\Service\Service;
use WHMCS\Database\Capsule;
use ZapMeSdk\Base as ZapMeSdk;
use ZapMeTeam\Whmcs\Actions\Actions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

if (!defined('WHMCS')) {
    die;
}

/** @deprecated */
class ZapMeModule
{
    private string $now;

    public function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
    }

    /**
     * Internal action for edit logs
     *
     * @param ParameterBag|null $post
     * 
     * @return string
     */
    private function internalActionEditLogs(ParameterBag $post = null): string
    {
        $clearlogs = $post->get('clearlogs');

        if ($clearlogs === null) {
            return alert('Ops! <b>Você não confirmou o procedimento.</b>', 'danger');
        }

        Capsule::table('mod_zapme_logs')->truncate();

        return alert('Tudo certo! <b>Procedimento efetuado com sucesso.</b>');
    }

    /**
     * External action for invoice reminder (using hook)
     *
     * @param ParameterBag|null $get
     * 
     * @return string
     */
    private function externalActionInvoiceReminder(ParameterBag $get = null): string
    {
        $invoicePaymentReminder = (new ZapMeHooks)->prepare('InvoicePaymentReminder')->InvoicePaymentReminder(['invoiceid' => $get->get('invoiceid')], true);

        if ($invoicePaymentReminder === true) {
            return alert('Tudo certo! <b>Procedimento efetuado com sucesso.</b>');
        }

        return alert('Ops! <b>O procedimento não foi realizado!</b> Confira os logs do sistema.', 'danger');
    }

    /**
     * External action for service ready
     *
     * @param ParameterBag|null $get
     * 
     * @return string
     */
    private function externalActionServiceReady(ParameterBag $get = null): string
    {
        $template = new ZapMeTemplateHandle('AfterModuleReady');
        $hooks    = new ZapMeHooks;
        $module   = $hooks->getModuleConfiguration();

        if ($template->templateStatus() === false) {
            return alert('Ops! <b>O procedimento não foi realizado!</b> Confira os logs do sistema.', 'danger');
        }

        $service = Service::find($get->get('serviceid'));
        $client  = $service['client'];
        $product = $service['product'];

        if (clientConsentiment('AfterModuleReady', $client, $module->clientconsentfieldid) === false) {
            return alert('Ops! <b>O procedimento não foi realizado!</b> Confira os logs do sistema.', 'danger');
        }

        $message = $template->defaultVariables($client)->serviceVariables($service, $product)->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $module->clientphonefieldid);

        $response = (new Base())
            ->withApi($module->api)
            ->withSecret($module->secret)
            ->sendMessage($phone, $message);

        if (isset($response['result']) && $response['result'] === 'created') {
            if ($module->logsystem == 1) {
                moduleSaveLog($message, 'aftermoduleready', $client->id);
            }
            return alert('Tudo certo! <b>Procedimento efetuado com sucesso.</b>');
        }

        if (ZAPME_MODULE_ACTIVITY_LOG === true) {
            logActivity('[ZapMe][AfterModuleReady] Envio de Mensagem: Erro: ' . $response['result']);
        }

        return alert('Ops! <b>O procedimento não foi realizado!</b> Confira os logs do sistema.', 'danger');
    }

    /**
     * External action for manual send message
     *
     * @param ParameterBag|null $post
     * 
     * @return string
     */
    private function externalActionManualMessage(ParameterBag $post = null): string
    {
        $template = new ZapMeTemplateHandle('AfterModuleReady');
        $hooks    = new ZapMeHooks;
        $module   = $hooks->getModuleConfiguration();

        $client = Client::find($post->get('userid'));

        if (clientConsentiment('AfterModuleReady', $client, $module->clientconsentfieldid) === false) {
            return alert('Ops! <b>O procedimento não foi realizado!</b> Confira os logs do sistema.', 'danger');
        }

        $message = $template->defaultVariables($client, $post->get('message'))->getTemplateMessage();
        $phone   = clientPhoneNumber($client, $module->clientphonefieldid);

        $response = (new Base())
            ->withApi($module->api)
            ->withSecret($module->secret)
            ->sendMessage($phone, $message);

        if (isset($response['result']) && $response['result'] === 'created') {
            if ($module->logsystem == 1) {
                moduleSaveLog($message, 'manualmessage', $client->id);
            }
            return alert('Tudo certo! <b>Procedimento efetuado com sucesso.</b>');
        }

        if (ZAPME_MODULE_ACTIVITY_LOG === true) {
            logActivity('[ZapMe][AfterModuleReady] Envio de Mensagem: Erro: ' . $response['result']);
        }

        return alert('Ops! <b>O procedimento não foi realizado!</b> Confira os logs do sistema.', 'danger');
    }
}
