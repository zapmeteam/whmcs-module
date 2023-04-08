<?php

namespace ZapMe\Whmcs\Module;

use WHMCS\User\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use ZapMe\Whmcs\DTO\ConfigurationDTO;

class WhmcsClient
{
    protected object $client;
    private ?ConfigurationDTO $module = null;

    public function __construct(int $id)
    {
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
            !$this->module->clientConsentFieldId ||
            $this->module->clientConsentFieldId == 0 ||
            empty($fields = $this->client->customFieldValues)
        ) {
            return true;
        }

        $value = collect($fields)
            ->map(function (object $field) {
                return [
                    'id'    => (int) $field->fieldId,
                    'value' => Str::of($field->value)
                        ->lower()
                        ->replace('ã', 'a')
                        ->__toString()
                ];
            })
            ->firstWhere('id', '=', $this->module->clientConsentFieldId)
            ->value;

        return in_array($value, ['n', 'nao']);
    }

    private function phone(): string
    {
        $original = sanitize($this->client->phonenumber);

        if (
            !$this->module->clientPhoneFieldId ||
            $this->module->clientPhoneFieldId == 0
        ) {
            return $original;
        }

        $value = collect($this->client->customFieldValues)
            ->map(function (object $field) {
                return [
                    'id'    => (int) $field->fieldId,
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
