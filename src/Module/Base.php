<?php

namespace ZapMe\Whmcs\Module;

use ZapMe\Whmcs\Traits\Alertable;
use ZapMeSdk\Base as ZapMeSdk;
use Illuminate\Support\Carbon;
use ZapMe\Whmcs\Traits\InteractWithCarbon;

class Base
{
    use Alertable;
    use InteractWithCarbon;

    protected ZapMeSdk $zapme;

    protected Carbon $carbon;

    public function __construct()
    {
        $this->zapme = (new ZapMeSdk())->toUrl(ZAPME_MODULE_API_URL);

        $this->newCarbonInstance();
    }
}
