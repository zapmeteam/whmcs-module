<?php

namespace ZapMe\Whmcs\DTO;

use Illuminate\Support\Carbon;

class ConfigurationDto
{
    public bool $configured           = false;
    public ?string $api               = null;
    public ?string $secret            = null;
    public ?bool $isActive            = null;
    public ?bool $logSystem           = null;
    public ?bool $logAutoRemove       = null;
    public ?int $clientPhoneFieldId   = null;
    public ?int $clientConsentFieldId = null;
    public ?array $account            = null;
    public ?Carbon $createdAt         = null;
    public ?Carbon $updatedAt         = null;

    public function __construct(
        bool $configured = false,
        ?string $api = null,
        ?string $secret = null,
        ?bool $isActive = null,
        ?bool $logSystem = null,
        ?bool $logAutoRemove = null,
        ?int $clientPhoneFieldId = null,
        ?int $clientConsentFieldId = null,
        ?array $account = null,
        ?Carbon $createdAt = null,
        ?Carbon $updatedAt = null,
    ) {
        $this->configured           = $configured;
        $this->api                  = $api;
        $this->secret               = $secret;
        $this->isActive             = $isActive;
        $this->logSystem            = $logSystem;
        $this->logAutoRemove        = $logAutoRemove;
        $this->clientPhoneFieldId   = $clientPhoneFieldId;
        $this->clientConsentFieldId = $clientConsentFieldId;
        $this->account              = $account;
        $this->createdAt            = $createdAt;
        $this->updatedAt            = $updatedAt;
    }
}
