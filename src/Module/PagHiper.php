<?php

namespace ZapMe\Whmcs\Module;

use WHMCS\Database\Capsule;

class PagHiper
{
    public function active(): bool
    {
        return Capsule::table('tblpaymentgateways')
            ->select('value')
            ->where('gateway', '=', 'paghiper')
            ->where('setting', '=', 'visible')
            ->first()
            ?->value === 'on';
    }
}
