<?php

namespace ZapMe\Whmcs\Actions\Module;

use Throwable;
use Punic\Exception;
use WHMCS\Database\Capsule;
use Symfony\Component\HttpFoundation\Request;
use ZapMe\Whmcs\Traits\InteractWithModuleActions;

class InternalActions
{
    use InteractWithModuleActions;

    public function editModuleConfigurations(Request $request): string
    {
        $post   = $request->request;
        $api    = $post->get('api');
        $secret = $post->get('secret');

        try {
            $response = $this->sdk()
                ->withApi($api)
                ->withSecret($secret)
                ->accountStatus();

            if ($response && $response['result'] !== 'success') {
                throw new Exception($response['result']);
            }

            $capsule = Capsule::table('mod_zapme');

            $capsule->truncate();
            $capsule->insert([
                'api'                     => $api,
                'secret'                  => $secret,
                'is_active'               => $post->get('is_active'),
                'log_system'              => $post->get('log_system'),
                'log_auto_remove'         => $post->get('log_auto_remove'),
                'client_phone_field_id'   => $post->get('client_phone_field_id'),
                'client_consent_field_id' => $post->get('client_consent_field_id'),
                'account'                 => serialize($response['data']),
                ...[
                    ...carbonToDatabase(),
                ],
            ]);

            return $this->success("Tudo certo! <b>Módulo configurado e atualizado com sucesso.</b>");
        } catch (Throwable $e) {
            throwlable($e);
        }

        return $this->danger("Ops! <b>Houve algum erro ao validar a sua API.</b> Verifique os logs do sistema.</b>");
    }

    public function editModuleTemplateConfigurations(Request $request): string //OK
    {
        $post     = $request->request;
        $template = $post->get('template');

        try {
            $capsule = Capsule::table('mod_zapme_templates');

            if (
                $capsule->where('id', '=', $template)
                    ->doesntExist()
            ) {
                return $this->danger("<b>Ops!</b> O template solicitado para edição não existe no banco de dados.");
            }

            $capsule->where('id', '=', $template)
                ->update([
                    'message'   => $post->get('message'),
                    'is_active' => $post->get('is_active'),
                    ...[
                        ...carbonToDatabase('updated_at'),
                    ],
                ]);

            return $this->success("Tudo certo! <b>Template atualizado com sucesso.</b>");
        } catch (Throwable $e) {
            throwlable($e);
        }

        return $this->danger("Ops! <b>Houve algum erro ao editar o template.</b> Verifique os logs do sistema.</b>");
    }

    public function editModuleLogsConfigurations(Request $request): string
    {
        $post         = $request->request;
        $confirmation = $post->get('clearlogs');

        try {
            if (!$confirmation) {
                return $this->danger("Ops! <b>Você não confirmou o procedimento.</b>");
            }

            Capsule::table('mod_zapme_logs')->truncate();

            return $this->success("Tudo certo! <b>Procedimento realizado com sucesso.</b>");
        } catch (Throwable $e) {
            throwlable($e);
        }

        return $this->danger("Ops! <b>Houve algum erro ao editar o template.</b> Verifique os logs do sistema.</b>");
    }
}
