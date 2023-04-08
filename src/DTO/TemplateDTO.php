<?php

namespace ZapMe\Whmcs\DTO;

use Illuminate\Support\Carbon;

class TemplateDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $code = null,
        public ?string $message = null,
        public ?bool $isActive = null,
        public ?object $structure = null,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {
        //
    }
}
