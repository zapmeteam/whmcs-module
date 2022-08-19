<?php

namespace ZapMeSdk\Actions\Messages;

use Exception;
use ZapMeSdk\Traits\PerformRequest;
use ZapMeSdk\Traits\ShareableConstructor;

class SendMessage
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
        return '/messages/create';
    }

    /**
     * @throws Exception
     */
    public function __invoke(string $phone, string $message, array $attachment = [])
    {
        $data = [
            'api'     => $this->api,
            'secret'  => $this->secret,
            'phone'   => $phone,
            'message' => $message,
        ];

        if (isset($attachment['file_content'])) {
            $data['file_content'] = $attachment['file_content'];
        }

        if (isset($attachment['file_extension'])) {
            $data['file_extension'] = $attachment['file_extension'];
        }

        return $this->request($this->path(), $data);
    }
}
