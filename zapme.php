<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/helper.php';

use WHMCS\User\Client;
use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Actions\Actions;
use ZapMe\Whmcs\Module\Template;
use ZapMe\Whmcs\Module\Configuration;
use Symfony\Component\HttpFoundation\Request;

if (!defined('WHMCS')) {
    die;
}

function zapme_config(): array
{
    return [
        'name'        => 'ZapMe',
        'description' => 'Módulo da ZapMe para o sistema WHMCS.',
        'version'     => '2.1.0',
        'language'    => 'portuguese-br',
        'author'      => 'ZapMe'
    ];
}

function zapme_activate(): array
{
    if (($phpVersion = phpversion()) < 8.0) {
        return ['status'  => 'error', 'description' => "PHP ({$phpVersion}) Incompatível. Versão Desejada: 8.0+"];
    }

    try {
        $now    = date('Y-m-d H:i:s');
        $schema = Capsule::schema();

        foreach (['mod_zapme', 'mod_zapme_templates', 'mod_zapme_logs'] as $table) {
            $schema->dropIfExists($table);
        }

        $schema->create('mod_zapme', function ($table) {
            $table->id();
            $table->string('api');
            $table->string('secret');
            $table->boolean('is_active')->default(false);
            $table->boolean('log_system')->default(false);
            $table->boolean('log_auto_remove')->default(false);
            $table->unsignedInteger('client_phone_field_id')->nullable();
            $table->unsignedInteger('client_consent_field_id')->nullable();
            $table->text('account')->nullable();
            $table->timestamps();
        });

        $schema->create('mod_zapme_templates', function ($table) {
            $table->id();
            $table->string('code');
            $table->binary('message');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $schema->create('mod_zapme_logs', function ($table) {
            $table->id();
            $table->string('code');
            $table->unsignedInteger('client_id');
            $table->binary('message');
            $table->timestamps();
        });

        $templates = [
            'InvoiceCreated'            => 'Olá %name%! Informamos que a fatura N° %invoiceid%, com vencimento em %duedate%, tem o valor total de R$ %value%, já encontra-se disponível em sua área do cliente para pagamento. Evite transtornos e efetue o pagamento até a data de vencimento.',
            'InvoicePaymentReminder'    => 'Olá %name%! Informamos que sua fatura N° %invoiceid% está em aberto há alguns dias em sua área do cliente, com o vencimento em %duedate% e o valor total de R$ %value%. Evite transtornos e efetue o pagamento da fatura.',
            'InvoicePaid'               => 'Olá %name%! Informamos que o pagamento de sua fatura N° %invoiceid%, foi confirmado com sucesso, agradecemos a sua confiança.',
            'InvoiceCancelled'          => 'Olá %name%! Informamos que a fatura N° %invoiceid%, com vencimento em %duedate% e o valor total de R$ %value% foi cancelada em sua conta. Para mais detalhes entre em contato com o nosso suporte via ticket em sua área do cliente',
            'InvoiceFirstOverdueAlert'  => 'Olá %name%! Informamos que sua fatura N° %invoiceid% está vencida há 1 dia e esta mensagem serve como um lembrete de primeiro aviso de fatura em atraso. O vencimento da fatura era em %duedate%, com o valor total de R$ %value%. O não pagamento desta fatura poderá acarretar em suspensão dos serviços vinculado a ela.',
            'InvoiceSecondOverdueAlert' => 'Olá %name%! Informamos que sua fatura N° %invoiceid% está vencida há 2 dias e esta mensagem serve como um lembrete de segundo aviso de fatura em atraso. O vencimento da fatura era em %duedate%, com o valor total de R$ %value%. O não pagamento desta fatura poderá acarretar em suspensão dos serviços vinculado a ela.',
            'InvoiceThirdOverdueAlert'  => 'Olá %name%! Informamos que sua fatura N° %invoiceid% está vencida há 3 dias e esta mensagem serve como um lembrete de terceiro e último aviso de fatura em atraso. O vencimento da fatura era em %duedate%, com o valor total de R$ %value%. Os serviços vinculados a esta fatura poderão ser suspensos e nenhum novo aviso será emitido.',
            'TicketOpen'                => 'Olá %name%! Recebemos o seu ticket de N° %ticket% em %date% as %hour% em nosso sistema. Aguarde, em breve um de nossos atendentes irá lhe atender.',
            'TicketAdminReply'          => 'Olá %name%! O seu ticket N° %ticket% foi respondido por nossa equipe neste exato momento (%date% - %hour%). Visite sua área de cliente para conferir maiores informações.',
            'AfterModuleCreate'         => 'Olá %name%! O seu produto %product% foi ativado! Para sua segurança os dados de acesso foram encaminhados ao seu e-mail neste momento. Caso não encontre entre em contato conosco.',
            'AfterModuleSuspend'        => 'Olá %name%! Informamos que o produto %product% foi suspenso, para mais informações acesse a área do cliente.',
            'AfterModuleUnsuspend'      => 'Olá %name%! Informamos que o produto %product% foi reativado, para mais informações acesse a área do cliente.',
            'AfterModuleTerminate'      => 'Olá %name%! Informamos que o produto %product% foi cancelado, para mais informações acesse a área do cliente.',
            'AfterModuleReady'          => 'Olá %name%! Informamos o serviço %product% encontra-se pronto para uso a partir de agora. Veja alguns detalhes sobre: Domínio: %domain%, IP: %ip%, Valor: R$ %value%, Vencimento: %duedate%. Outros detalhes como: nome de usuário e senha do serviço foram encaminhados ao seu e-mail por uma questão de segurança. Caso não tenha recebido entre em contato com nosso suporte via ticket em sua área do cliente',
            'ClientAdd'                 => 'Olá %name%! Seja bem-vindo a %companyname%! Agradecemos a preferência em nossa plataforma, ficamos contentes com a sua chegada.',
            'ClientLogin'               => 'Olá %name%! Identificamos um acesso à sua conta originado do endereço de IP %ipaddr%. Caso desconheça este IP recomendamos alterar sua senha imediatamente.',
            'ClientAreaPageLogin'       => 'Olá %name%! Identificamos uma falha de login originada deste IP %ipaddr% em %date% as %hour% sua conta: %email%. Caso você desconheça este IP ou tenha certeza que não foi você recomendamos alterar sua senha imediatamente.',
            'ClientChangePassword'      => 'Olá %name%! Informamos que a senha da sua conta (%email%) foi alterada neste momento e por isso achamos prudente te notificar. IP: %ipaddr%, Data da alteração: %date%, Hora: %hour%. Se você desconhece essa ação recomendamos recuperar sua senha neste momento e em seguida entre em contato com nosso suporte via ticket em sua área do cliente.',
        ];

        $connection = Capsule::connection();

        foreach ($templates as $key => $value) {
            $connection->transaction(fn ($handler) =>$handler->table('mod_zapme_templates')->insert([
                'code'            => $key,
                'message'         => $value,
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now
            ]));
        }

        return ['status' => 'success', 'description' => 'Módulo Ativado.'];
    } catch (Exception $e) {
        return ['status' => 'error', 'description' => 'Erro: ' . $e->getMessage()];
    }
}

function zapme_deactivate(): array
{
    try {
        foreach (['mod_zapme', 'mod_zapme_templates', 'mod_zapme_logs'] as $table) {
            Capsule::schema()->dropIfExists($table);
        }

        return ['status' => 'success', 'description' => 'Módulo Desativado.'];
    } catch (Exception $e) {
        return ['status' => 'error', 'description' => 'Erro: ' . $e->getMessage()];
    }
}

function zapme_output($vars)
{
    $request = Request::createFromGlobals();

    $tab = $request->get('action') ?? $request->get('tab');
    $tab = $tab === 'configuration' || $tab === 'manualmessage' ? null : $tab;

    echo (new Actions($request))->execute();

    $module = (new Configuration())->fromDto();

    switch ($tab) {
        case 'templates':
            $templates = (new Template())->fromDto();
            break;
        case 'logs':
            $logs = Capsule::table('mod_zapme_logs')->oldest('id')->get();
            break;
    }

    #dd($templates);

    if (!$tab) $fields = Capsule::table('tblcustomfields')->where('type', '=', 'client')->get();
?>
    <?php if (!$module->configured) : ?>
        <div class="alert alert-info text-center"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> <b>O módulo não encontra-se configurado para uso!</b> Certifique-se de configurar o módulo para que o mesmo funcione corretamente.</div>
    <?php endif; ?>
    <?php if ($module->configured && !$module->isActive) : ?>
        <div class="alert alert-danger text-center"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> <b>ATENÇÃO!</b> O módulo encontra-se configurado, mas <b>o status está "Desativado".</b> Nenhuma mensagem será enviada até que o status esteja <b>"Ativo".</b></div>
    <?php endif; ?>
    <?php if ($module->configured) : ?>
        <div class="signin-apps-container">
            <p class="text-center font-weight-bold">Status do Serviço</p>
            <div class="row">
                <div class="col-sm-12 col-md-3">
                    <div class="app">
                        <div class="logo-container">
                            <h5>SERVIÇO</h5>
                        </div>
                        <div class="content-container">
                            <?php $service = $module->account['service']['status'] === 'active'; ?>
                            <div class="description"><label class="label label-<?= $service ? 'success' : 'danger' ?>"><span><?= $service ? 'ATIVO' : 'INATIVO' ?></span></label></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3">
                    <div class="app">
                        <div class="logo-container">
                            <h5>PLANO</h5>
                        </div>
                        <div class="content-container">
                            <div class="description"><?= $module->account['service']['plan'] ?? 'N/A' ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3">
                    <div class="app">
                        <div class="logo-container">
                            <h5>VENCIMENTO</h5>
                        </div>
                        <div class="content-container">
                            <?php $duedate = $module->account['service']['duedate'] ?? null; ?>
                            <div class="description"><?= $duedate ? date('d/m/Y', strtotime($duedate)) : 'N/A' ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3">
                    <div class="app">
                        <div class="logo-container">
                            <h5>WHATSAPP</h5>
                        </div>
                        <div class="content-container">
                            <?php $authenticated = $module->account['auth']['status'] === true; ?>
                            <div class="description"><label class="label label-<?= $authenticated ? 'success' : 'danger' ?>"><span><?= $authenticated ? 'AUTENTICADO' : 'NÃO AUTENTICADO' ?></span></label></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <ul class="nav nav-tabs admin-tabs" role="tablist">
        <li <?= !$tab ? 'class="active"' : '' ?>>
            <a class="tab-top" href="addonmodules.php?module=zapme" id="configurations" data-tab-id="1">
                <i class="fa fa-cog"></i>
                Configuração
            </a>
        </li>
        <li <?= $tab === 'templates' ? 'class="active"' : '' ?> <?= !$module->configured ? 'style="display: none;' : '' ?>>
            <a class="tab-top" href="addonmodules.php?module=zapme&tab=templates" id="templates" data-tab-id="2">
                <i class="fa fa-comments"></i>
                Templates
            </a>
        </li>
        <li <?= $tab === 'logs' ? 'class="active"' : '' ?> <?= !$module->configured ? 'style="display: none;' : '' ?>>
            <a class="tab-top" href="addonmodules.php?module=zapme&tab=logs" id="logs" data-tab-id="3">
                <i class="fa fa-list"></i>
                Logs
            </a>
        </li>
    </ul>
    <div class="tab-content admin-tabs">
        <!-- Configurations -->
        <div class="tab-pane <?= selected(!$tab, 'active') ?>" id="configurations">
            <div class="auth-container" style="margin: auto !important; border-top: none;">
                <div class="row">
                    <form action="addonmodules.php?module=zapme&internalconfig=true&action=configuration" method="post">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputConfirmPassword">Status</label>
                                    <select class="form-control" name="is_active">
                                        <option value="1" <?= selected($module->configured && $module->isActive) ?>>Ativado</option>
                                        <option value="0" <?= selected($module->configured && !$module->isActive) ?>>Desativado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputConfirmPassword">API</label>
                                    <input type="text" name="api" class="form-control" value="<?= $module->api ?>" placeholder="Insira sua chave de API" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputConfirmPassword">Chave Secreta</label>
                                    <input type="text" name="secret" class="form-control" value="<?= $module?->secret ?>" placeholder="Insira sua sua Chave Secreta" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputConfirmPassword">Registros de Logs</label>
                                    <select class="form-control" name="log_system">
                                        <option value="1" <?= selected($module->configured && $module->logSystem) ?>>Sim</option>
                                        <option value="0" <?= selected($module->configured && !$module->logSystem) ?>>Não</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputConfirmPassword">Auto. Remoção dos Registros de Logs</label>
                                    <select class="form-control" name="log_auto_remove">
                                        <option value="1" <?= selected($module->configured && $module->logAutoRemove) ?>>Sim</option>
                                        <option value="0" <?= selected($module->configured && !$module->logAutoRemove) ?>>Não</option>
                                    </select>
                                    <i class="fas fa-question-circle text-danger" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-html="true" title="Apaga os registros de logs do módulo todo dia primeiro de cada mês, através das ações de hooks do WHMCS."></i>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputConfirmPassword">Seletor de Telefone</label>
                                    <select class="form-control" name="client_phone_field_id">
                                        <option value="0">- Padrão do WHMCS</option>
                                        <?php foreach ($fields as $field) : ?>
                                            <option value="<?= $field->id ?>" <?= selected($module->configured && $module->clientPhoneFieldId == $field->id) ?>>#<?= $field->id ?> - Nome: <?= $field->fieldname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <i class="fas fa-question-circle text-danger" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-html="true" title="Permite usar um campo customizado de Telefone diferente do padrão do WHMCS. <b>Este campo necessita que o formato do telefone seja: DDI + DDD + Telefone.</b> Caso não seja identificado o DDI no campo customizado o sistema irá obter o DDI do campo de Telefone padrão do WHMCS."></i>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputConfirmPassword">Conscentimento de Mensagens</label>
                                    <select class="form-control" name="client_consent_field_id">
                                        <option value="0">- Nenhum</option>
                                        <?php foreach ($fields as $field) : ?>
                                            <option value="<?= $field->id ?>" <?= selected($module->configured && $module->clientConsentFieldId == $field->id) ?>>#<?= $field->id ?> - Nome: <?= $field->fieldname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <i class="fas fa-question-circle text-danger" aria-hidden="true" data-toggle="tooltip" data-placement="top" data-html="true" title="Campo custommizado de cadastro para viabilizar o conscentimento do cliente para receber ou não as mensagens encaminhadas pelo sistema. Se definido como <b>Nenhum</b> o envio será efetuado normalmente.<br><br>O valor do campo customizado para seleção do cliente deve conter: Sim,Não (com ou sem acento). <b>Caso o cliente selecione Não, então os envios serão abortados.</b>"></i>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">SALVAR</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Templates -->
        <div class="tab-pane <?= selected($tab === 'templates', 'active') ?>" id="templates">
            <table id="tabletempalte" class="table table-striped table-responsive" style="width: 100% !important">
                <thead>
                <tr>
                    <td>#</td>
                    <td>Nome</td>
                    <td>Descrição</td>
                    <td>Status</td>
                    <td></td>
                </tr>
                </thead>
                <tbody>
                <?php /** @var \ZapMe\Whmcs\DTO\TemplateDTO $template */
                    foreach ($templates as $template): ?>
                    <tr>
                        <th><?= $template->id ?></th>
                        <th><?= $template->name ?></th>
                        <th><?= $template->structure->description ?></th>
                        <th>
                            <?php $class = $template->isActive ? 'success' : 'danger'; ?>
                            <i class="fa fa-check-circle text-<?= $class; ?>"></i>
                        </th>
                        <th> <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editmodal-<?= $template->id ?>"><i class="fa fa-eye" aria-hidden="te"></i></button> </th>
                    </tr>
                        <div class="modal fade" id="editmodal-<?= $template->id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">#<?= $template->id ?> - <?= $template->name ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="addonmodules.php?module=zapme&internalconfig=true&action=templates" method="post">
                                        <input type="hidden" name="template" value="<?= $template->id ?>">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Mensagem</label>
                                                <textarea class="form-control" name="message" rows="8" style="resize: none;" required><?= $template->message ?></textarea>
                                            </div>
                                            <hr>
                                            <p>Variáveis Disponíveis</p>
                                            <?php echo $template->structure->print; ?>
                                            <?php if (isset($template->structure->paghiper)) : ?>
                                                <div class="alert alert-warning text-center">
                                                    Para anexar o boleto bancário do PagHiper neste template escreva: %paghiper_boleto% em qualquer parte da mensagem
                                                </div>
                                                <small class="text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> O boleto bancário do PagHiper só será enviado se o sistema atender as seguintes condições: <b>I.</b> A fatura for superior à R$ 3,00. <b>II.</b> A fatura estiver com o método de pagamento PagHiper. <b>III.</b> O módulo do PagHiper estiver ativado e marcado como visível. <b>Caso contrário, as variáveis %paghiper_barcode% e %paghiper_boleto% serão removidas do envio</b></small>
                                            <?php endif; ?>
                                            <hr>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select class="form-control" name="is_active">
                                                    <option value="1" <?= selected($template->isActive) ?>>Ativado</option>
                                                    <option value="0" <?= selected(!$template->isActive) ?>>Desativado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Salvar</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Logs -->
        <div class="tab-pane <?= selected($tab === 'logs', 'active') ?>" id="logs">
            <button class="btn btn-danger btn-sm" style="margin: 0px 0px 15px 0px;" data-toggle="modal" data-target="#clearlogs">APAGAR REGISTROS</button>
            <table id="tablelog" class="table table-striped table-responsive" style="width: 100% !important">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Cliente</td>
                        <td>Data do Envio</td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log) :
                        $client = Client::find($log->client_id); ?>
                        <tr>
                            <th><?= $log->id ?></th>
                            <th><a href="clientssummary.php?userid=<?= $client->id ?>" target=_blank><?= $client->firstname . ' ' . $client->lastname . ' (#' . $log->clientid . ')' ?></a></th>
                            <th><?= date('d/m/Y H:i:s', strtotime($log->created_at)) ?></th>
                            <th> </a> <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#seelogmessage-<?= $log->id ?>"><i class="fa fa-eye" aria-hidden="true"></i></button> </th>
                        </tr>
                        <div class="modal fade" id="seelogmessage-<?= $log->id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">#<?= $log->id ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <?= nl2br($log->message) ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="modal" id="clearlogs" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Apagar Registros de Logs</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="addonmodules.php?module=zapme&internalconfig=true&action=logs" method="post">
                                <p class="justify">Este procedimento irá remover todos os registros de logs do módulo existentes em seu banco de dados. <b>Para prosseguir confirme o procedimento abaixo:</b></p>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input id="my-input" class="form-check-input" type="checkbox" name="clearlogs">
                                        <label class="form-check-label text-danger">Estou ciente e desejo prosseguir com a limpeza dos logs do sistema</label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p class="footer" style="margin-top: 10px !important;"><a href="https://zapme.com.br/" target="_blank"><b>ZapMe</b></a>, Versão: <b><?= $vars['version'] ?></b></p>
    <script>
        $(document).ready(function() {
            $("#tablelog").DataTable({
                "pageLength": 25,
                "order": [
                    [0, "desc"]
                ],
                responsive: true
            });
            $("#tabletempalte").DataTable({
                "pageLength": 25,
                "order": [
                    [0, "asc"]
                ],
                responsive: true
            });
        });
    </script>
<?php } ?>