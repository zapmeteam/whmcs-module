<?php

namespace ZapMe\Whmcs\Module;

use WHMCS\Database\Capsule;
use Illuminate\Support\Carbon;
use ZapMe\Whmcs\DTO\ConfigurationDTO;

class Configuration
{
    protected ?object $configuration = null;

    public function __construct()
    {
        $this->configuration = Capsule::table('mod_zapme')->first();
    }

    public function fromDto(): ConfigurationDTO
    {
        return (new ConfigurationDTO(
            configured: $this->configuration !== null,
            api: $this->configuration?->api,
            secret: $this->configuration?->secret,
            isActive: $this->configuration?->is_active == 1,
            logSystem: $this->configuration?->log_system == 1,
            logAutoRemove: $this->configuration?->log_auto_remove == 1,
            clientPhoneFieldId: $this->configuration?->client_phone_field_id,
            clientConsentFieldId: $this->configuration?->client_consent_field_id,
            account: $this->configuration ? unserialize($this->configuration->account) : null,
            createdAt: Carbon::parse($this->configuration?->created_at),
            updatedAt: Carbon::parse($this->configuration?->updated_at),
        ));
    }
}
