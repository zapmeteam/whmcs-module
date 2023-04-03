<?php

namespace ZapMe\Whmcs;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\Traits\Alert;
use Illuminate\Support\Carbon;

class Sdk
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

    public function carbonToDatabase(bool $createdAt = true): array
    {
        $date = $this->carbon->format('Y-m-d H:i:s');

        $updated = ['updated_at' => $date];

        if (!$createdAt) {
            return $updated;
        }

        return array_merge($updated, ['created_at' => $date]);
    }
}
