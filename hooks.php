<?php

require __DIR__ . '/vendor/autoload.php';

use WHMCS\User\Client;
use WHMCS\Service\Service;
use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Actions\Hooks;

if (!defined('WHMCS')) {
	die;
}

$whmcs = whmcs_version();

add_hook('InvoiceCreated', 1, fn ($vars) => (new Hooks('InvoiceCreated'))->dispatch($vars));

add_hook('InvoiceCancelled', 1, function ($vars) {
	//$zapMeHooks->prepare('invoicecancelled')->dispatch($vars);
});

add_hook('InvoicePaymentReminder', 1, function ($vars) {
	//$zapMeHooks->prepare('InvoicePaymentReminder')->dispatch($vars);
});

add_hook('InvoicePaid', 1, function ($vars) {
	//$zapMeHooks->prepare('InvoicePaid')->dispatch($vars);
});

add_hook('TicketOpen', 1, function ($vars) {
	//$zapMeHooks->prepare('TicketOpen')->dispatch($vars);
});

add_hook('TicketAdminReply', 1, function ($vars) {
	//$zapMeHooks->prepare('TicketAdminReply')->dispatch($vars);
});

add_hook('AfterModuleCreate', 1, function ($vars) {
	//$zapMeHooks->prepare('AfterModuleCreate')->dispatch($vars);
});

add_hook('AfterModuleSuspend', 1, function ($vars) {
	//$zapMeHooks->prepare('AfterModuleSuspend')->dispatch($vars);
});

add_hook('AfterModuleUnsuspend', 1, function ($vars) {
	//$zapMeHooks->prepare('AfterModuleUnsuspend')->dispatch($vars);
});

add_hook('AfterModuleTerminate', 1, function ($vars) {
	//$zapMeHooks->prepare('AfterModuleTerminate')->dispatch($vars);
});

add_hook('ClientAdd', 1, fn ($vars) => (new Hooks('ClientAdd', $whmcs))->dispatch($vars));

/*add_hook('ClientLogin', 1, function ($vars) {
	if (isset($_SESSION['adminid'])) {
		return;
	}

	$zapMeHooks->prepare('ClientLogin')->dispatch($vars);
});*/

add_hook($whmcs >= 8 ? 'UserLogin' : 'ClientLogin', 1, fn ($vars) => (new Hooks('ClientLogin', $whmcs))->dispatch($vars));

add_hook($whmcs >= 8 ? 'ClientLoginShare' : 'ClientAreaPageLogin', 1, fn ($vars) => (new Hooks('ClientAreaPageLogin', $whmcs))->dispatch($vars));

add_hook('ClientAreaHomepage', 1, function ($vars) {
//    dd(Client::find($_SESSION['uid']));
//
//    $file = ZAPME_MODULE_PATH . "/translations/english.php";
//
//    if (!file_exists($file)) {
//        return;
//    }
//
//    $language = collect(require $file);
//
//    dd($language);
});

add_hook('ClientChangePassword', 1, function ($vars) {
	//$zapMeHooks->prepare('ClientChangePassword')->dispatch($vars);
});

add_hook('AdminInvoicesControlsOutput', 1, function ($vars) {
//	if (isset($zapMeModule->id) && $zapMeModule->status == 1) {
//		$invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
//		if ($invoice->status === 'Unpaid') {
//			return '<br /><br /><a href="addonmodules.php?module=zapme&externalaction=invoicereminder&invoiceid=' . $vars['invoiceid'] . '" target="_blank" class="btn btn-warning">[ZapMe] Fatura em Aberto</a>';
//		}
//	}
});

add_hook('AdminClientServicesTabFields', 1, function ($vars) {
//	if (isset($zapMeModule->id) && $zapMeModule->status == 1) {
//		$service = Service::find($vars['id']);
//		if ($service->domainStatus === 'Active') {
//			$div = '';
//			$div .= '<div class="row">';
//			$div .= '<div class="col-md-1" style="margin-bottom: 10px !important;">';
//			$div .= '<a href="addonmodules.php?module=zapme&externalaction=serviceready&serviceid=' . $vars['id'] . '" class="btn btn-warning">[ZapMe] Serviço Pronto</a>';
//			$div .= '</div>';
//			$div .= '</div>';
//			echo $div;
//		}
//	}
});

add_hook('AdminAreaClientSummaryPage', 1, function ($vars) {
//	if (isset($zapMeModule->id) && $zapMeModule->status == 1) {
//		$html = '';
//		$html .= '<a href="#" data-toggle="modal" data-target="#zapmemessage" target="_blank" class="btn btn-warning">[ZapMe] Envio Manual de Mensagem</a>';
//		$html .= '
//		<div class="modal fade" id="zapmemessage">
//			<div class="modal-dialog">
//				<div class="modal-content">
//					<div class="modal-header">
//						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
//						<h5 class="modal-title">[ZapMe] Envio de Mensagem Manual</h4>
//					</div>
//					<form action="addonmodules.php?module=zapme&internalconfig=false&action=manualmessage" class="form-horizontal" method="post">
//						<input type="hidden" name="userid" value="' . $vars['userid'] . '" />
//						<div class="modal-body">
//							<div class="row">
//								<div class="col-md-12">
//									<label>Mensagem</label>
//									<textarea name="message" rows="10" class="form-control" style="resize: none;" required></textarea>
//								</div>
//							</div>
//							<p style="margin-top: 10px !important;">Variáveis Disponíveis</p>
//							<div class="alert alert-info text-center">
//								%name% - Nome do cliente (completo)<br>
//								%firstname% - Primeiro nome do cliente<br>
//								%lastname% - Último nome do cliente<br>
//								%email% - E-mail do cliente<br>
//								%company% - Compania do Cliente<br>
//							</div>
//						</div>
//						<div class="modal-footer">
//							<button type="submit" class="btn btn-success">Enviar</button>
//							<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
//						</div>
//					</form>
//				</div>
//			</div>
//		</div>
//		';
//		return $html;
//	}
});

add_hook('EmailPreSend', 1, function ($vars) {
//	$template = $vars['messagename'];
//
//	if (mb_strpos($template, 'Invoice Overdue Notice') !== false) {
//		$type = explode(' ', $template);
//		$hook = $type[0];
//
//		if ($hook === 'First' || $hook === 'Second' || $hook === 'Third') {
//			$zapMeHooks->prepare('Invoice' . $hook . 'OverDueAlert')->dispatch($vars);
//		}
//	}
});

add_hook('DailyCronJob', 1, function ($vars) {
	//$zapMeHooks->prepare('DailyCronJob')->dispatch($vars);
});
