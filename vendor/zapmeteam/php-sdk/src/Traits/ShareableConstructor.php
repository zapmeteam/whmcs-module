<?php

namespace ZapMeSdk\Traits;

trait ShareableConstructor
{
    /**
     * The ZapMe API url.
     *
     * @var string
     */
    private string $url = 'https://api.zapme.com.br';

    /**
     * ZapMe API.
     *
     * @var string|null
     */
    private ?string $api = null;

    /**
     * ZapMe Secret Key.
     *
     * @var string|null
     */
    private ?string $secret = null;

    public function __construct(string $url, string $api, string $secret)
    {
        $this->url    = $url;
        $this->api    = $api;
        $this->secret = $secret;
    }
}
