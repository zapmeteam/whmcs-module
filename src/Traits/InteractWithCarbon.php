<?php

namespace ZapMe\Whmcs\Traits;

use Illuminate\Support\Carbon;

trait InteractWithCarbon
{
    protected Carbon $carbon;

    public function carbon(): Carbon
    {
        $this->carbon = Carbon::now('America/Sao_Paulo');

        return $this->carbon;
    }

    public function createdAt(): array
    {
        return ['created_at' => $this->carbon->format('Y-m-d H:i:s')];
    }

    public function updatedAt(): array
    {
        return ['updated_at' => $this->carbon->format('Y-m-d H:i:s')];
    }
}