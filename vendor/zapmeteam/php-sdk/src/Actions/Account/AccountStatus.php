<?php

namespace ZapMeSdk\Actions\Account;

use Exception;
use ZapMeSdk\Traits\PerformRequest;
use ZapMeSdk\Traits\ShareableConstructor;

class AccountStatus
{
    use PerformRequest;
    use ShareableConstructor;

    /**
     * Path related with the action.
     *
     * @return string
     */
    public function path(): string
    {
        return '/status';
    }

    /**
     * Send the request to the ZapMe.
     *
     * @return array
     */
    public function __invoke(): array
    {
        return $this->request($this->path());
    }
}
