<?php

require __DIR__ . '/vendor/autoload.php';

use WHMCS\User\Client;
use WHMCS\Service\Service;
use WHMCS\Database\Capsule;
use Illuminate\Support\Str;
use ZapMe\Whmcs\Actions\Hooks\HookExecution;
use ZapMe\Whmcs\Module\Configuration;

if (!defined('WHMCS')) {
	die;
}

$whmcs  = whmcs_version();
$module = (new Configuration())->dto();

add_hook('InvoiceCreated', 1, fn ($vars) => (new HookExecution('InvoiceCreated'))->dispatch($vars));

add_hook('InvoiceCancelled', 1, fn ($vars) => (new HookExecution('InvoiceCancelled'))->dispatch($vars));

add_hook('InvoicePaid', 1, fn ($vars) => (new HookExecution('InvoicePaid'))->dispatch($vars));

add_hook('InvoicePaymentReminder', 1, fn ($vars) => (new HookExecution('InvoicePaymentReminder'))->dispatch($vars));

add_hook('TicketOpen', 1, fn ($vars) => (new HookExecution('TicketOpen'))->dispatch($vars));

add_hook('TicketAdminReply', 1, fn ($vars) => (new HookExecution('TicketAdminReply'))->dispatch($vars));

add_hook('AfterModuleCreate', 1, fn ($vars) => (new HookExecution('AfterModuleCreate'))->dispatch($vars));

add_hook('AfterModuleSuspend', 1, fn ($vars) => (new HookExecution('AfterModuleSuspend'))->dispatch($vars));

add_hook('AfterModuleUnsuspend', 1, fn ($vars) => (new HookExecution('AfterModuleUnsuspend'))->dispatch($vars));

add_hook('AfterModuleTerminate', 1, fn ($vars) => (new HookExecution('AfterModuleTerminate'))->dispatch($vars));

add_hook('ClientAdd', 1, fn ($vars) => (new HookExecution('ClientAdd', $whmcs))->dispatch($vars));

add_hook($whmcs >= 8 ? 'UserLogin' : 'ClientLogin', 1, fn ($vars) => (new HookExecution('ClientLogin', $whmcs))->dispatch($vars));

add_hook($whmcs >= 8 ? 'ClientLoginShare' : 'ClientAreaPageLogin', 1, fn ($vars) => (new HookExecution('ClientAreaPageLogin', $whmcs))->dispatch($vars));

add_hook($whmcs >= 8 ? 'UserChangePassword' : 'ClientChangePassword', 1, fn ($vars) => (new HookExecution('ClientChangePassword'))->dispatch($vars));

add_hook('DailyCronJob', 1, fn ($vars) => (new HookExecution('DailyCronJob'))->dispatch($vars));

add_hook('EmailPreSend', 1, function ($vars) {
	$message    = $vars['messagename'];
    $stringable = Str::of($message);

    if (!$stringable->contains('Invoice Overdue Notice')) {
        return;
    }

    $hook = ucfirst($stringable->explode(' ')->first());

    if (!in_array($hook, ['First', 'Second', 'Third'])) {
        return;
    }

    (new HookExecution("Invoice{$hook}OverDueAlert"))->dispatch($vars);
});

add_hook('AdminInvoicesControlsOutput', 1, function ($vars) use ($module) {
    if (!$module || !$module->isActive) {
        return;
    }

    $invoice = Capsule::table('tblinvoices')
        ->where('id', '=', $vars['invoiceid'])
        ->first();

    if ($invoice->status !== 'Unpaid') {
        return;
    }

    return "
        <a href=\"addonmodules.php?module=zapme&action=invoicereminder&type=external&invoiceid=$invoice->id\" target=\"_blank\" class=\"btn btn-warning\">
            <i class=\"fa fa-bell\"></i> Lembrete de Fatura em Aberto
        </a>
    ";
});

add_hook('AdminClientServicesTabFields', 1, function ($vars) use ($module) {
    if (!$module || !$module->isActive || Service::find($vars['id'])->domainStatus !== 'Active') {
        return;
    }

    echo "<div class=\"row\">
            <div class=\"col-md-1\" style=\"margin-bottom: 10px !important;\">
                <a href=\"addonmodules.php?module=zapme&action=serviceready&type=external&service={$vars['id']}\" class=\"btn btn-warning\">
                    <i class=\"fa fa-bell\"></i>
                    Serviço Pronto
                </a>
            </div>
        </div>";
});

add_hook('AdminAreaClientSummaryPage', 1, function ($vars) use ($module) {
    if (!$module || !$module->isActive) {
        return;
    }

    return "
        <a href=\"#\" data-toggle=\"modal\" data-target=\"#zapmemessage\" target=\"_blank\" class=\"btn btn-warning\">
            <i class=\"fa fa-bell\"></i>	
            Envio Manual de Mensagem
        </a>
		<div class=\"modal fade\" id=\"zapmemessage\">
			<div class=\"modal-dialog\">
				<div class=\"modal-content\">
					<div class=\"modal-header\">
						<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">×</span></button>
						<h5 class=\"modal-title\">
						    <i class=\"fa fa-bell\"></i>	
						    Envio de Mensagem Manual
						</h5>
					</div>
					<form action=\"addonmodules.php?module=zapme&action=manualmessage&type=external\" class=\"form-horizontal\" method=\"post\">
						<input type=\"hidden\" name=\"userid\" value=\"{$vars['userid']}\" />
						<div class=\"modal-body\">
							<div class=\"row\">
								<div class=\"col-md-12\">
									<label>Mensagem</label>
									<textarea name=\"message\" rows=\"10\" class=\"form-control\" style=\"resize: none;\" required></textarea>
								</div>
							</div>
							<p style=\"margin-top: 10px !important;\">Variáveis Disponíveis</p>
							<div class=\"alert alert-info text-center\">
								%name% - Nome do cliente (completo)<br>
								%firstname% - Primeiro nome do cliente<br>
								%lastname% - Último nome do cliente<br>
								%email% - E-mail do cliente<br>
								%company% - Compania do Cliente<br>
							</div>
                            <div class=\"row\">
                                <div class=\"col-md-12\">
                                    <input class=\"form-check-input\" type=\"checkbox\" name=\"ignore_consent\" id=\"ignore_consent\">
                                    <label class=\"form-check-label text-danger\" for=\"ignore_consent\">Ignorar Consentimento de Mensagem</label>
                                    <i class=\"fas fa-question-circle text-danger\" aria-hidden=\"true\" data-toggle=\"tooltip\" data-placement=\"top\" data-html=\"true\" title=\"O módulo irá ignorar o conscentimento de mensagens do cliente.\"></i>
                                </div>
                            </div>
						</div>
						<div class=\"modal-footer\">
							<button type=\"submit\" class=\"btn btn-success\">Enviar</button>
							<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>
						</div>
					</form>
				</div>
			</div>
		</div>";
});
