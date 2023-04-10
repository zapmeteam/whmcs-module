<?php

namespace ZapMe\Whmcs\Module;

use WHMCS\User\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use ZapMe\Whmcs\DTO\ConfigurationDTO;

class WhmcsClient
{
    public function __construct(
        int $id,
        private ?ConfigurationDTO $configuration = null,
        protected ?object $client = null
    ) {
        $this->client = Client::find($id);
    }

    public function configuration(ConfigurationDTO $module): self
    {
        $this->configuration = $module;

        return $this;
    }

    public function get(?string $index = null): bool|string|Collection|null
    {
        $this->configuration ??= (new Configuration())->fromDto();

        if (!$this->client) {
            return null;
        }

        return collect([
            'whmcs'   => $this->client,
            'phone'   => $this->phone(),
            'consent' => $this->consent(),
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

        $value = Str::of($fields->firstWhere('id', '=', $this->configuration->clientConsentFieldId)?->value ?? '')
            ->lower()
            ->replace('ã', 'a');

        return in_array($value, ['s', 'sim']);
    }

    //TODO: refactor like above method
    private function phone(): string
    {
        $original = sanitize($this->client->phonenumber);

        if (
            !$this->configuration->clientPhoneFieldId || $this->configuration->clientPhoneFieldId == 0
        ) {
            return $original;
        }

        $value = collect($this->client->customFieldValues)
            ->map(function (object $field) {
                return [
                    'id'    => (int)$field->fieldId,
                    'value' => sanitize($field->value)
                ];
            })
            ->firstWhere('id', '=', $this->configuration->clientPhoneFieldId)
            ->value ?? null;

        if (!$value) {
            return $original;
        }

        $phone = explode('.', $this->client->phonenumber);
        $ddi   = sanitize($phone[0]);

        return $ddi . $value;
    }
}
