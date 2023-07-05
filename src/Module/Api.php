<?php

namespace ZapMe\Whmcs\Module;

class Api
{
    /** @var string */
    protected $url;

    /** @var string */
    protected $api;

    /** @var string */
    protected $secret;

    public function toUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function withApi(string $api): self
    {
        $this->api = $api;

        return $this;
    }

    public function withSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function sendMessage(string $phone, string $message, array $attachment = []): array
    {
        $curl = curl_init("{$this->url}/messages/create");

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            'api'            => $this->api,
            'secret'         => $this->secret,
            'phone'          => $phone,
            'message'        => $message,
            'file_content'   => $attachment['file_content']   ?? null,
            'file_extension' => $attachment['file_extension'] ?? null,
        ]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($curl);

        if (!$result) {
            $error = curl_error($curl);

            logActivity("[ZapMe] Erro: $error");
        }

        return json_decode($result, true);
    }

    public function accountStatus(): array
    {
        $curl = curl_init("{$this->url}/status?api={$this->api}&secret={$this->secret}");

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPGET, true);
        $result = curl_exec($curl);

        if (!$result) {
            $error = curl_error($curl);

            logActivity("[ZapMe] Erro: $error");
        }

        return json_decode($result, true);
    }
}
