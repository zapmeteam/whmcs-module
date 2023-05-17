<?php

namespace ZapMe\Whmcs\DTO;

class TemplateDto
{
    /** @var int|null */
    public $id = null;

    /** @var string|null */
    public $name = null;

    /** @var string|null */
    public $code = null;

    /** @var string|null */
    public $message = null;

    /** @var bool|null */
    public $isActive = null;

    /** @var object|null */
    public $structure = null;

    /** @var string|null */
    public $createdAt = null;

    /** @var string|null */
    public $updatedAt = null;

    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?string $code = null,
        ?string $message = null,
        ?bool $isActive = null,
        ?object $structure = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
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
