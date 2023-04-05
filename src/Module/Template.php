<?php

namespace ZapMe\Whmcs\Module;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use ZapMe\Whmcs\DTO\TemplateDTO;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use WHMCS\Database\Capsule;

class Template
{
    protected Collection|null $template = null;

    protected PagHiper $paghiper;

    public function __construct(?string $code = null)
    {
        $this->template = Capsule::table('mod_zapme_templates')
            ->when($code && ctype_alpha($code), fn (Builder $query) => $query->where('code', '=', $code))
            ->when($code && ctype_digit($code), fn (Builder $query) => $query->where('id', '=', $code))
            ->get();

        $this->paghiper = new PagHiper();
    }

    public function fromDto(): TemplateDTO|Collection
    {
        return $this->template->transform(function (object $item) {
            $item = $this->structure($item);

            return (new TemplateDTO(
                id: $item->id,
                name: $item->structure?->name ?? $item->code,
                code: $item->code,
                message: $item->message,
                isActive: $item->is_active == 1,
                isConfigurable: $item->is_configurable == 1,
                configurations: $item->configurations ? unserialize($item->configurations) : null,
                structure: $item->structure,
                createdAt: Carbon::parse($item->created_at),
                updatedAt: Carbon::parse($item->updated_at),
            ));
        });
    }

    public function fromDatabase(): ?object
    {
        return $this->template->transform(function (object $item) {
            $item = $this->structure($item);

            $item->configurations = unserialize($item->configurations);
            $item->created_at     = Carbon::parse($item->created_at);
            $item->updated_at     = Carbon::parse($item->updated_at);

            return $item;
        });
    }

    private function structure(object $template): object
    {
        collect(glob(ZAPME_MODULE_PATH . "/src/Helper/Template/Structures/*.php"))
            ->filter(fn (string $file) => Str::of($file)->contains($template->code))
            ->each(function (string $file) use (&$template) {
                $class = Str::of($file)
                    ->afterLast('/')
                    ->beforeLast('.php')
                    ->__toString();

                $class               = "ZapMe\\Whmcs\\Helper\\Template\\Structures\\" . $class;
                $template->structure = (new $class)::execute($this->paghiper->active());
            });

        return $template;
    }
}
