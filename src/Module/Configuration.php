<?php

namespace ZapMe\Whmcs\Module;

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
            optional($this->configuration)->api,
            optional($this->configuration)->secret,
            optional($this->configuration)->is_active            == 1,
            optional($this->configuration)->log_system          == 1,
            optional($this->configuration)->log_auto_remove == 1,
            optional($this->configuration)->client_phone_field_id,
            optional($this->configuration)->client_consent_field_id,
            $this->configuration? unserialize($this->configuration->account) : null,
            now()->parse(optional($this->configuration)->created_at),
            now()->parse(optional($this->configuration)->updated_at),
        ));
    }
}
