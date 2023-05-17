<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use Exception;
use Throwable;
use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class DailyCronJob extends HookExecutionStructure
{
    public function execute($vars): bool
    {
        $date = (int)date('d');

        if (
            $this->configuration->logAutoRemove && $date === 1
        ) {
            $this->log("Limpeza de Logs");

            Capsule::table('mod_zapme_logs')->truncate();
        }

        try {
            $response = $this->zapme->accountStatus();

            if (!data_get($response, 'status')) {
                throw new Exception('Não foi possível validar a API.');
            }

            Capsule::table('mod_zapme')
                ->where('id', '=', 1)
                ->update([
                    'account'    => serialize($response['data']),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $this->log("Atualização de Dados do Serviço");
        } catch (Throwable $e) {
            throwlable($e);
        }

        return true;
    }
}
