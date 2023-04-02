<?php

namespace ZapMeSdk\Actions\Contacts;

use Exception;
use ZapMeSdk\Traits\PerformRequest;
use ZapMeSdk\Traits\ShareableConstructor;

class CreateContact
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
        return '/contacts';
    }

    /**
     * Send the request to the ZapMe.
     *
     * @param  string  $name
     * @param  string  $phone
     * @param  string  $status
     * @param mixed $group
     *
     * @return array
     */
    public function __invoke(string $name, string $phone, string $status = 'active', $group = null): array
    {
        $data = [
            'name'   => $name,
            'phone'  => $phone,
            'status' => $status,
        ];

        if ($group !== null) {
            $data['group'] = $group;
        }

        return $this->request($this->path(), $data);
    }
}