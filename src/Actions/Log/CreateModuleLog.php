<?php

namespace ZapMe\Whmcs\Actions\Log;

use WHMCS\Database\Capsule;

class CreateModuleLog
{
    public static function execute(string $message, string $hook, int $client): void
    {
        $now = now()->format('Y-m-d H:i:s');

        Capsule::table('mod_zapme_logs')->insert([
            'code'      => $hook,
            'client_id' => $client,
            'message'   => $message,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
