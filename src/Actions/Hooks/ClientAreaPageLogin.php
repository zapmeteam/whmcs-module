<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use WHMCS\User\Client;
use WHMCS\Database\Capsule;
use Illuminate\Support\Str;
use ZapMe\Whmcs\Helper\Hooks\AbstractHookStructure;
use ZapMe\Whmcs\Helper\Template\TemplateParseVariable;

class ClientAreaPageLogin extends AbstractHookStructure
{

    public function execute(mixed $vars): void
    {
        if ($this->impersonate()) {
            return;
        }

        $log = Capsule::table('tblactivitylog')
            ->where('user', '=', 'System')
            ->oldest('date')
            ->first();

        if (!Str::of($log->description)->contains('Failed Login Attempt')) {
            return;
        }

        if (($this->client = $this->client($log->userid)) === null) {
            return;
        }

        if ($this->client->get('consent') === false) {
            $this->log('O cliente: ({id}) {name} optou por não receber alertas');
            return;
        }

        if (!$this->template->isActive) {
            return;
        }

        $template = (new TemplateParseVariable($this->template))
            ->fromClient($this->client);

//        if (clientConsentiment($this->hook, $client, $this->module->clientconsentfieldid) === false) {
//            return;
//        }

//        $template = new ZapMeTemplateHandle($this->hook);
//
//        if ($template->templateStatus() === false) {
//            return;
//        }
//
//        if ($template->templateHasValidConfigurations() === true) {
//            if (
//                $template->controlByClient($client) === false ||
//                $template->controlByPartsOfEmail($client) === false ||
//                $template->controlByWeekDay() === false ||
//                $template->controlByClientStatus($client) === false
//            ) {
//                return;
//            }
//        }
//
//        $message = $template->defaultVariables($client)->clientVariables($client, $vars)->getTemplateMessage();
//        $phone   = clientPhoneNumber($client, $this->module->clientphonefieldid);
//
//        $logDate     = new DateTime($log->date);
//        $currentDate = new DateTime;
//        $dateDiff    = $logDate->diff($currentDate);
//
//        if ($dateDiff->d == 0 && $dateDiff->i == 0 && ($dateDiff->s < 2)) {
//            $this->endOfDispatch($message, $phone, $client->id);
//        }
//
//        logActivity(var_export($_SESSION, true));
    }
}
