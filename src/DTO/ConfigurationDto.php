<?php

namespace ZapMe\Whmcs\DTO;

use Illuminate\Support\Carbon;

class ConfigurationDto
{
    /** @var bool */
    public $configured = false;

    /** @var string|null */
    public $api = null;

    /** @var string|null */
    public $secret = null;

    /** @var bool|null */
    public $isActive = null;

    /** @var bool|null */
    public $logSystem = null;

    /** @var bool|null */
    public $logAutoRemove = null;

    /** @var int|null */
    public $clientPhoneFieldId = null;

    /** @var int|null */
    public $clientConsentFieldId = null;

    /** @var array|null */
    public $account = null;

    /** @var Carbon|null */
    public $createdAt = null;

    /** @var Carbon|null */
    public $updatedAt = null;

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
        ?Carbon $updatedAt = null
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
