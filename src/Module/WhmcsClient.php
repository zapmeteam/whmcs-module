<?php

namespace ZapMe\Whmcs\Module;

use WHMCS\User\Client;
use Illuminate\Support\Str;
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

        return collect([
            'whmcs'   => $this->client,
            'phone'   => $this->phone(),
            'consent' => $this->consent(),
            'new'     => $this->new(),
        ])->when($index, function (Collection $collection) use ($index) {
            return $collection->get($index);
        });
    }

    private function consent(): bool
    {
        if (
            !$this->configuration->clientConsentFieldId || $this->configuration->clientConsentFieldId == 0 || empty($fields = $this->client->customFieldValues)
        ) {
            return true;
        }

        $value = Str::of($fields->firstWhere('id', '=', optional($this->configuration->clientConsentFieldId)->value ?? ''))
            ->lower()
            ->replace('ã', 'a');

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

        $value = $fields->firstWhere('id', '=', optional($this->configuration->clientPhoneFieldId)->value ?? null);

        if (!$value) {
            return $original;
        }

        $phone = explode('.', $this->client->phonenumber);
        $ddi   = sanitize($phone[0]);

        return $ddi . sanitize($value);
    }

    private function new(): bool
    {
        $now = now();

        return $now
            ->parse($this->client->created_at)
            ->diffInMinutes($now) <= 1;
    }
}
