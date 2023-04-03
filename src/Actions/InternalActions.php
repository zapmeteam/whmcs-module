<?php

namespace ZapMe\Whmcs\Actions;

use Throwable;
use ZapMe\Whmcs\Sdk;
use Punic\Exception;
use WHMCS\Database\Capsule;
use Symfony\Component\HttpFoundation\Request;

class InternalActions extends Sdk
{
    public function editModuleConfigurations(Request $request): string
    {
        $post   = $request->request;
        $api    = $post->get('api');
        $secret = $post->get('secret');

        try {
            $response = $this->zapme
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
                ...$this->carbonToDatabase(),
            ]);

            return $this->success('Tudo certo! <b>Módulo configurado e atualizado com sucesso.</b>');
        } catch (Throwable $e) {
            throwlable($e);
        }

        return $this->danger('Ops! <b>Houve algum erro ao validar a sua API.</b> Verifique os logs do sistema.</b>');
    }

    public function editModuleTemplateConfigurations()
    {

    }

    public function editModuleTemplateRulesConfigurations()
    {

    }

    public function editModuleLogsConfigurations()
    {

    }
}
