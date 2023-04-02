<?php

namespace ZapMeSdk\Actions\Contacts;

use Exception;
use ZapMeSdk\Traits\PerformRequest;
use ZapMeSdk\Traits\ShareableConstructor;

class DestroyContact
{
    use PerformRequest;
    use ShareableConstructor;

    /**
     * Path related with the action.
     *
     * @param  int  $id
     *
     * @return string
     */
    public function path(int $id): string
    {
        return sprintf('/contacts/%s', $id);
    }

    /**
     * Send the request to the ZapMe.
     *
     * @param  int  $id
     *
     * @return array
     */
    public function __invoke(int $id): array
    {
        $this->method('DELETE');

        return $this->request($this->path($id));
    }
}
