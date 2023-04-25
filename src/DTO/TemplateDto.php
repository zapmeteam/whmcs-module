<?php

namespace ZapMe\Whmcs\DTO;

use Illuminate\Support\Carbon;

class TemplateDto
{
    public ?int $id           = null;
    public ?string $name      = null;
    public ?string $code      = null;
    public ?string $message   = null;
    public ?bool $isActive    = null;
    public ?object $structure = null;
    public ?Carbon $createdAt = null;
    public ?Carbon $updatedAt = null;

    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?string $code = null,
        ?string $message = null,
        ?bool $isActive = null,
        ?object $structure = null,
        ?Carbon $createdAt = null,
        ?Carbon $updatedAt = null,
    ) {
        $this->id        = $id;
        $this->name      = $name;
        $this->code      = $code;
        $this->message   = $message;
        $this->isActive  = $isActive;
        $this->structure = $structure;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}
