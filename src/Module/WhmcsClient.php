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
        private ?ConfigurationDTO $module = null,
        protected ?object $client = null
    ) {
        $this->client = Client::find($id);
    }

    public function module(ConfigurationDTO $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function get(?string $index = null): bool|string|Collection|null
    {
        $this->module ??= (new Configuration())->fromDto();

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
            !$this->module->clientConsentFieldId || $this->module->clientConsentFieldId == 0 || empty($fields = $this->client->customFieldValues)
        ) {
            return true;
        }

        $value = Str::of($fields->firstWhere('id', '=', $this->module->clientConsentFieldId)?->value ?? '')
            ->lower()
            ->replace('ã', 'a');

        return in_array($value, ['s', 'sim']);
    }

    private function phone(): string
    {
        $original = sanitize($this->client->phonenumber);

        if (
            !$this->module->clientPhoneFieldId || $this->module->clientPhoneFieldId == 0
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
            ->firstWhere('id', '=', $this->module->clientPhoneFieldId)
            ->value ?? null;

        if (!$value) {
            return $original;
        }

        $phone = explode('.', $this->client->phonenumber);
        $ddi   = sanitize($phone[0]);

        return $ddi . $value;
    }
}
