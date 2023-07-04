<?php

namespace ZapMe\Whmcs\Module;

use DateTime;
use WHMCS\User\Client;
use Illuminate\Support\Collection;
use ZapMe\Whmcs\DTO\ConfigurationDto;

class WhmcsClient
{
    /** @var ConfigurationDto|null */
    private $configuration = null;

    /** @var object|null */
    protected $client = null;

    public function __construct(int $id, ?ConfigurationDto $configuration = null, ?object $client = null)
    {
        $this->configuration = $configuration;
        $this->client        = $client;

        $this->client = Client::find($id);
    }

    public function configuration(ConfigurationDto $module): self
    {
        $this->configuration = $module;

        return $this;
    }

    /**
     * @param string|null $index
     * @return bool|string|Collection|null
     */
    public function get(?string $index = null)
    {
        $this->configuration = $this->configuration ?? (new Configuration())->dto();

        if (!$this->client) {
            return null;
        }

        $collect = collect([
            'whmcs'   => $this->client,
            'phone'   => $this->phone(),
            'consent' => $this->consent(),
            'new'     => $this->new(),
        ]);

        if ($index) {
            return $collect->get($index);
        }

        return $collect;
    }

    private function consent(): bool
    {
        if (
            !$this->configuration->clientConsentFieldId || $this->configuration->clientConsentFieldId == 0 || empty($fields = $this->client->customFieldValues)
        ) {
            return true;
        }

        $value = $this->find($fields->toArray(), $this->configuration->clientConsentFieldId);

        if (!$value) {
            return true;
        }

        $value = str_replace('ã', 'a', strtolower($value));

        return in_array($value, ['s', 'sim']);
    }

    private function phone(): string
    {
        $original = sanitize($this->client->phonenumber);

        if (
            !$this->configuration->clientPhoneFieldId || $this->configuration->clientPhoneFieldId == 0 || empty($fields = $this->client->customFieldValues)
        ) {
            return $original;
        }

        $value = $this->find($fields->toArray(), $this->configuration->clientPhoneFieldId);

        if (!$value) {
            return $original;
        }

        $phone = explode('.', $this->client->phonenumber);
        $ddi   = sanitize($phone[0]);

        return $ddi . sanitize($value);
    }

    private function new(): bool
    {
        $now     = new DateTime();
        $compare = new DateTime($this->client->created_at);

        return $compare->diff($now)->i <= 1;
    }

    private function find(array $fields, int $id): ?string
    {
        if (empty($fields)) {
            return null;
        }

        return array_values(array_filter($fields, function ($field) use ($id) {
            if ((int) $field['fieldid'] === $id) {
                return $field;
            }
        }))[0]['value'] ?? null;
    }
}
