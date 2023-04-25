<?php

namespace ZapMe\Whmcs\Traits;

use Illuminate\Support\Carbon;

trait InteractWithCarbon
{
    protected ?Carbon $carbon = null;

    public function carbon(): Carbon
    {
        $this->carbon = Carbon::now('America/Sao_Paulo');

        return $this->carbon;
    }

    public function createdAt(): array
    {
        if (!$this->carbon) {
            $this->carbon();
        }

        return ['created_at' => $this->carbon->format('Y-m-d H:i:s')];
    }

    public function updatedAt(): array
    {
        if (!$this->carbon) {
            $this->carbon();
        }

        return ['updated_at' => $this->carbon->format('Y-m-d H:i:s')];
    }
}
