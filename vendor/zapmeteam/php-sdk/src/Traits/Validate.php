<?php

namespace ZapMeSdk\Traits;

use Exception;

trait Validate
{
    /**
     * @throws Exception
     */
    public function validate(): void
    {
        if ($this->api === '' || $this->api === null) {
            throw new Exception('The API is required.');
        }

        if ($this->secret === '' || $this->secret === null) {
            throw new Exception('The Secret Key is required.');
        }

        if ($this->url === '' || $this->url === null) {
            throw new Exception('The URL is required.');
        }

        if (strpos($this->url, 'http') === false) {
            throw new Exception('The URL has an incorrect format.');
        }
    }
}
