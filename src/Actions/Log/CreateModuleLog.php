<?php

namespace ZapMe\Whmcs\Actions\Log;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Traits\InteractWithCarbon;

class CreateModuleLog
{
    use InteractWithCarbon;

    public static function execute(string $message, string $hook, int $client): void
    {
        $class = new static();
        $class->carbon();

        Capsule::table('mod_zapme_logs')->insert([
            'code'      => $hook,
            'client_id' => $client,
            'message'   => $message,
            ...[
                ...$class->createdAt(),
                ...$class->updatedAt()
            ]
        ]);
    }
}
