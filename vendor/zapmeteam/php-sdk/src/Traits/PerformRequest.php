<?php

namespace ZapMeSdk\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

trait PerformRequest
{
    /**
     * The default request method.
     *
     * @var string
     */
    private string $method = 'POST';

    /**
     * Handles the request method.
     *
     * @param  string  $method
     * @return void
     */
    private function method(string $method = 'POST'): void
    {
        $this->method = $method;
    }

    /**
     * Send the request to the ZapMe.
     *
     * @param  string  $path
     * @param  array  $data
     *
     * @return array
     */
    private function request(string $path, array $data = []): array
    {
        $data += ['api'=> $this->api, 'secret' => $this->secret];

        try {
            $response = (new Client([
                'base_uri' => $this->url,
                'timeout'  => 5,
            ]))->request($this->method, $path, ['form_params' => $data]);
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
