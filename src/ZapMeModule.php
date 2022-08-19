<?php

namespace ZapMeTeam\Whmcs;

use ZapMeSdk\Base;
use WHMCS\User\Client;
use WHMCS\Service\Service;
use WHMCS\Database\Capsule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

if (!defined('WHMCS')) {
    die('Denied access');
}

class ZapMeModule
{
    /** * @var string */
    private $now;

    public function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
    }

    /**
     * Handle incoming request of module
     *
     * @param Request $request
     * 
     * @return void
     */
    public function handleRequest(Request $request)
    {
        $method = $request->getMethod();

        if ($method === 'POST') {
            if ($request->get('internalconfig') === null) {
                return;
            }

            switch ($request->get('action')) {
                case 'configuration':
                    return $this->internalActionEditConfigurations($request->request);
                case 'templates':
                    return $this->internalActionEditTemplates($request->request);
                case 'editrules':
                    return $this->internalActionEditTemplateRules($request->request);
                case 'logs':
                    return $this->internalActionEditLogs($request->request);
                case 'manualmessage':
                    return $this->externalActionManualMessage($request->request);
            }
        }

        if ($request->get('externalaction') === null) {
            return;
        }

        switch ($request->get('externalaction')) {
            case 'invoicereminder':
                return $this->externalActionInvoiceReminder($request->query);
            case 'serviceready':
                return $this->externalActionServiceReady($request->query);
        }
    }

    /**
     * Internal action for edit conifgurations
     *
     * @param ParameterBag|null $post
     * 
     * @return void
     */
    private function internalActionEditConfigurations(ParameterBag $post = null)
    {
        $api    = $post->get('api');
        $secret = $post->get('secret');

        $result = (new Base())
            ->withApi($api)
            ->withSecret($secret)
            ->accountStatus();

        if (isset($result['result']) && $result['result'] !== 'success') {
            if (ZAPMEMODULE_ACTIVITYLOG === true) {
                logActivity('[ZapMe] Erro: ' . $result['result']);
            }
            return alert('Ops! <b>Houve algum erro ao validar a sua API.</b> Verifique os logs do sistema e contate o suporte da ZapMe.</b>', 'danger');
        }

        $service = serialize([
            'status'  => $result['data']['service']['status'],
            'duedate' => $result['data']['service']['duedate'],
            'plan'    => $result['data']['service']['plan'],
            'auth'    => $result['data']['auth']['status'],
        ]);

        Capsule::table('mod_zapme')->truncate();

        Capsule::table('mod_zapme')->insert([
            'api'                  => $post->get('api'),
            'secret'               => $post->get('secret'),
            'status'               => (int) $post->get('status'),
            'logsystem'            => (int) $post->get('logsystem'),
            'logautoremove'        => (int) $post->get('logautoremove'),
            'clientconsentfieldid' => (int) $post->get('clientconsentfieldid'),
            'clientphonefieldid'   => (int) $post->get('clientphonefieldid'),
            'service'              => $service,
            'created_at'           => $this->now,
            'updated_at'           => $this->now
        ]);

        return alert('Tudo certo! <b>Módulo configurado e atualizado com sucesso.</b>');
    }

    /**
     * Internal action for edit templates
     *
     * @param ParameterBag|null $post
     * 
     * @return string
     */
    private function internalActionEditTemplates(ParameterBag $post = null): string
    {
        $templateId = (int) $post->get('messageid');

        if (!Capsule::table('mod_zapme_templates')->where('id', $templateId)->exists()) {
            return alert('<b>Ooops!</b> O template solicitado para edição não existe no banco de dados.', 'danger');
        }

        Capsule::table('mod_zapme_templates')->where('id', $templateId)->update([
            'message'    => $post->get('message'),
            'status'     => (int) $post->get('status'),
            'updated_at' => $this->now
        ]);

        return alert('Tudo certo! <b>Template #' . $templateId . ' editado com sucesso.</b>');
    }

    /**
     * Internal action for edit template rules
     *
     * @param ParameterBag|null $post
     * 
     * @return string
     */
    private function internalActionEditTemplateRules(ParameterBag $post = null): string
    {
        $template = Capsule::table('mod_zapme_templates')->where('id', $post->get('template'))->first();
        $templateDescriptions = templatesConfigurations($template->code);

        if (!isset($templateDescriptions['rules'])) {
            return alert('Ops! <b>O template selecionado <b>(#' . $template->id . ')</b> não possui regras de envio.', 'danger');
        }

        $post->remove('token');
        $post->remove('template');

        $post = $post->all();

        foreach ($templateDescriptions['rules'] as $rule => $informations) {
            if ($informations['field']['type'] === 'text') {
                $post[$informations['id']] = trim($post[$informations['id']], ',');
            }
        }

        Capsule::table('mod_zapme_templates')->where('id', $template->id)->update([
            'configurations' => serialize($post),
            'updated_at'     => $this->now
        ]);

        return alert('Tudo certo! <b>Procedimento efetuado com sucesso.</b>');
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

        if (ZAPMEMODULE_ACTIVITYLOG === true) {
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

        if (ZAPMEMODULE_ACTIVITYLOG === true) {
            logActivity('[ZapMe][AfterModuleReady] Envio de Mensagem: Erro: ' . $response['result']);
        }

        return alert('Ops! <b>O procedimento não foi realizado!</b> Confira os logs do sistema.', 'danger');
    }
}
