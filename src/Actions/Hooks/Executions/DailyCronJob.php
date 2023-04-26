<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use Exception;
use Throwable;
use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class DailyCronJob extends HookExecutionStructure
{
    public function execute(mixed $vars): bool
    {
        if (
            $this->configuration->logAutoRemove && now()->startOfMonth()->isToday()
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
                    'account' => serialize($response['data']),
                    ...[
                        ...carbonToDatabase('updated_at')
                    ],
                ]);
        } catch (Throwable $e) {
            throwlable($e);
        }

        $this->log("Atualização de Dados do Serviço");

        return true;
    }
}
