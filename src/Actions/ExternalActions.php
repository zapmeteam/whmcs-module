<?php

namespace ZapMe\Whmcs\Actions;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\ZapMeHooks;
use ZapMe\Whmcs\Module\Base;
use ZapMe\Whmcs\ZapMeTemplateHandle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class ExternalActions extends Base
{
    public function sendInvoiceReminderMessage(Request $request): string
    {
        $invoice = Capsule::table('tblinvoices')
            ->where('id', $request->get('invoiceid'))
            ->first();

        if ($invoice->status !== 'Unpaid') {
            return $this->danger("Ops! <b>O procedimento não foi realizado!</b> A fatura não está em aberto.");
        }

        $result = (new Hooks('InvoicePaymentReminder'))->dispatch(['invoiceid' => $invoice->id]);

        if ($result) {
            return $this->success("Tudo certo! <b>Procedimento efetuado com sucesso.</b>");
        }

        return $this->danger("Ops! <b>O procedimento não foi realizado!</b> Confira os logs do sistema.");
    }

    //TODO: refactor
    private function externalActionServiceReady(ParameterBag $get = null): string
    {
        $template = new ZapMeTemplateHandle('AfterModuleReady');
        $hooks    = new ZapMeHooks();
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

    //TODO: refactor
    private function externalActionManualMessage(ParameterBag $post = null): string
    {
        $template = new ZapMeTemplateHandle('AfterModuleReady');
        $hooks    = new ZapMeHooks();
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
