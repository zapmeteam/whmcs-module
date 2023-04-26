<?php

namespace ZapMe\Whmcs\Actions\Module;

use Throwable;
use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Module\WhmcsClient;
use ZapMe\Whmcs\Module\Configuration;
use ZapMe\Whmcs\Actions\Hooks\HookExecution;
use ZapMe\Whmcs\Actions\Log\CreateModuleLog;
use Symfony\Component\HttpFoundation\Request;
use ZapMe\Whmcs\Traits\InteractWithModuleActions;

class ExternalActions
{
    use InteractWithModuleActions;

    public function sendInvoiceReminderMessage(Request $request): string
    {
        $invoice = Capsule::table('tblinvoices')
            ->where('id', $request->get('invoiceid'))
            ->first();

        if ($invoice->status !== 'Unpaid') {
            return $this->danger("Ops! <b>O procedimento não foi realizado!</b> A fatura não está em aberto.");
        }

        $result = (new HookExecution('InvoicePaymentReminder'))->dispatch(['invoiceid' => $invoice->id]);

        if ($result) {
            return $this->success("Tudo certo! <b>Procedimento efetuado com sucesso.</b>");
        }

        return $this->danger("Ops! <b>O procedimento não foi realizado!</b> Confira os logs do sistema.");
    }

    public function sendManualMessage(Request $request): string
    {
        $post   = $request->request;
        $whmcs  = (new WhmcsClient($post->get('userid')))->get();
        $client = $whmcs->get('whmcs');

        if (!$post->get('ignore_consent') && !$whmcs->get('consent')) {
            return $this->danger("Ops! <b>O procedimento não foi realizado!</b> O cliente não deseja receber alertas via WhatsApp.");
        }

        $message       = $post->get('message');
        $configuration = (new Configuration())->dto();

        $message = str_replace('%name%', $client->fullname, $message);
        $message = str_replace('%firstname%', $client->firstname, $message);
        $message = str_replace('%lastname%', $client->lastname, $message);
        $message = str_replace('%email%', $client->email, $message);
        $message = str_replace('%company%', $client->companyName, $message);

        try {
            $this->sdk($configuration)->sendMessage($whmcs->get('phone'), $message);

            if ($configuration->logSystem) {
                CreateModuleLog::execute(
                    $message,
                    'manual',
                    $client->id,
                );
            }

            return $this->success("Tudo certo! <b>Procedimento realizado com sucesso.</b>");
        } catch (Throwable $e) {
            throwlable($e);
        }

        return $this->danger("Ops! <b>O procedimento não foi realizado!</b> Confira os logs do sistema.");
    }

    public function sendServiceReadyMessage(Request $request): string
    {
        $result = (new HookExecution('AfterModuleReady'))->dispatch(['service' => $request->get('service')]);

        if ($result) {
            return $this->success("Tudo certo! <b>Procedimento efetuado com sucesso.</b>");
        }

        return $this->danger("Ops! <b>O procedimento não foi realizado!</b> Confira os logs do sistema.");
    }
}
