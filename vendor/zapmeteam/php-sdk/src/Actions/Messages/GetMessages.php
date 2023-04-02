<?php

namespace ZapMeSdk\Actions\Messages;

use ZapMeSdk\Traits\PerformRequest;
use ZapMeSdk\Traits\ShareableConstructor;

class GetMessages
{
    use PerformRequest;
    use ShareableConstructor;

    /**
     * Path related with the action.
     *
     * @param  bool  $paginate
     * @param  int  $page
     * @param  int  $quantity
     * @return string
     */
    public function path(bool $paginate = false, int $page = 1, int $quantity = 10): string
    {
        $url = sprintf('/messages?api=%s&secret=%s', $this->api, $this->secret);

        if ($paginate) {
            $url .= sprintf('&paginate=true&page=%s&quantity=%s', $page, $quantity);
        }

        return $url;
    }


    /**
     * Send the request to the ZapMe.
     *
     * @param  bool  $paginate
     * @param  int  $page
     * @param  int  $quantity
     *
     * @return array
     */
    public function __invoke(bool $paginate = false, int $page = 1, int $quantity = 10): array
    {
        $this->method('GET');
        
        return $this->request($this->path($paginate, $page, $quantity));
    }
}
