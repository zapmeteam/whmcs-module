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
     * Send the request to the ZapMe.
     *
     * @param  string  $phone
     * @param  string  $message
     * @param  array  $attachment
     *
     * @return array
     */
    public function __invoke(string $phone, string $message, array $attachment = []): array
    {
        $data = [
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
