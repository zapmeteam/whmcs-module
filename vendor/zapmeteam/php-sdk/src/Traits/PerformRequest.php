<?php

namespace ZapMeSdk\Traits;

use Exception;

trait PerformRequest
{
    use Validate;

    /**
     * The default request method.
     *
     * @var string
     */
    private $method = 'POST';

    /**
     * Handles the request method.
     *
     * @param  string  $method
     * @return void
     */
    private function method(string $method = 'POST')
    {
        $this->method = $method;
    }

    /**
     * Make the request to the ZapMe Api.
     *
     * @throws Exception
     */
    private function request(string $path, array $data = [])
    {
        $this->validate();

        $curl = curl_init($this->url . $path);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($curl);

        return json_decode($result, true);
    }
}
