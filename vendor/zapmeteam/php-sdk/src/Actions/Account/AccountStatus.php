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
     * @throws Exception
     */
    public function __invoke()
    {
        $data = [
            'api'    => $this->api,
            'secret' => $this->secret,
        ];

        return $this->request($this->path(), $data);
    }
}
