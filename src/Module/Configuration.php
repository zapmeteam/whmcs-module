<?php

namespace ZapMe\Whmcs\Module;

use DateTime;
use WHMCS\Database\Capsule;
use ZapMe\Whmcs\DTO\ConfigurationDto;

class Configuration
{
    /** @var object|null */
    protected $configuration = null;

    public function __construct()
    {
        $this->configuration = Capsule::table('mod_zapme')->first();
    }

    public function dto(): ConfigurationDto
    {
        return (new ConfigurationDto(
            $this->configuration !== null,
            $this->configuration->api                     ?? null,
            $this->configuration->secret                  ?? null,
            $this->configuration->is_active               ?? 0,
            $this->configuration->log_system              ?? 0,
            $this->configuration->log_auto_remove         ?? 0,
            $this->configuration->client_phone_field_id   ?? 0,
            $this->configuration->client_consent_field_id ?? 0,
            $this->configuration ? unserialize($this->configuration->account) : null,
            (new DateTime($this->configuration->created_at ?? 'now'))->format('Y-m-d H:i:s'),
            (new DateTime($this->configuration->updated_at ?? 'now'))->format('Y-m-d H:i:s'),
        ));
    }
}
