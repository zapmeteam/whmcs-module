<?php

namespace ZapMe\Whmcs\Helper\Template;

use ZapMe\Whmcs\DTO\TemplateDTO;

class TemplateParseVariable
{
    public function __construct(
        protected TemplateDTO $template
    ) {
    }

    public function usingDefault(): self
    {
        return $this;
    }

    public function fromClient(): self
    {
        return $this;
    }

    public function fromTicket(): self
    {
        return $this;
    }

    public function fromService(): self
    {
        return $this;
    }

    public function fromProduct(): self
    {
        return $this;
    }

    public function message(string $name, array $arguments): self
    {
        return $this;
    }
}
