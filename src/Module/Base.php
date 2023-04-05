<?php

namespace ZapMe\Whmcs\Module;

use ZapMe\Whmcs\Traits\Alert;
use ZapMeSdk\Base as ZapMeSdk;
use Illuminate\Support\Carbon;

class Base
{
    use Alert;

    protected ZapMeSdk $zapme;

    protected Carbon $carbon;

    public function __construct()
    {
        $this->zapme  = (new ZapMeSdk())->toUrl(ZAPME_MODULE_API_URL);

        $this->carbon = Carbon::now();
        $this->carbon->timezone('America/Sao_Paulo');
    }

    public function carbon(bool $createdAt = true): array
    {
        $date    = $this->carbon->format('Y-m-d H:i:s');
        $updated = ['updated_at' => $date];

        if (!$createdAt) {
            return $updated;
        }

        return array_merge($updated, ['created_at' => $date]);
    }
}
