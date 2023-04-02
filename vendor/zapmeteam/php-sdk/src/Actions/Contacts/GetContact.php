<?php

namespace ZapMeSdk\Actions\Contacts;

use Exception;
use ZapMeSdk\Traits\PerformRequest;
use ZapMeSdk\Traits\ShareableConstructor;

class GetContact
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
        return sprintf('/contacts/%s?api=%s&secret=%s', $id, $this->api, $this->secret);
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
        $this->method('GET');
        
        return $this->request($this->path($id));
    }
}
