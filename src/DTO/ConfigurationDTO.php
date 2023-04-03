<?php

namespace ZapMe\Whmcs\DTO;

use Illuminate\Support\Carbon;

class ConfigurationDTO
{
    public function __construct(
        public bool $configured = false,
        public ?string $api = null,
        public ?string $secret = null,
        public ?bool $isActive = null,
        public ?bool $logSystem = null,
        public ?bool $logAutoRemove = null,
        public ?int $clientPhoneFieldId = null,
        public ?int $clientConsentFieldId = null,
        public ?array $account = null,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {
        //
    }
}
