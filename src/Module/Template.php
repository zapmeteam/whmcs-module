<?php

namespace ZapMe\Whmcs\Module;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use ZapMe\Whmcs\DTO\TemplateDTO;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use WHMCS\Database\Capsule;

class Template extends Base
{
    protected Collection|null $template = null;

    public function __construct(?string $code = null)
    {
        parent::__construct();

        $this->template = Capsule::table('mod_zapme_templates')
            ->when($code, fn (Builder $query) => $query->where('code', '=', $code))
            ->get();
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
                isActive: $item->status == 1,
                isConfigurable: $item->is_configurable == 1,
                configurations: $item->configurations ? unserialize($item->configurations) : null,
                structure: $item->structure,
                createdAt: Carbon::parse($item->created_at),
                updatedAt: Carbon::parse($item->updated_at),
            ));
        })->count() == 1 ? $this->template->first() : $this->template;
    }

    public function fromDatabase(): ?object
    {
        return $this->template->transform(function (object $item) {
            $item = $this->structure($item);

            $item->configurations = unserialize($item->configurations);
            $item->created_at     = Carbon::parse($item->created_at);
            $item->updated_at     = Carbon::parse($item->updated_at);

            return $item;
        })->count() == 1 ? $this->template->first() : $this->template;
    }

    private function structure(object $template): object
    {
        collect(glob(ZAPME_MODULE_PATH . "/src/Helper/Template/*.php"))
            ->filter(fn (string $file) => Str::of($file)->contains($template->code))
            ->each(function (string $file) use (&$template) {
                $class = Str::of($file)
                    ->afterLast('/')
                    ->beforeLast('.php')
                    ->__toString();

                $class               = "ZapMe\\Whmcs\\Helper\\Template\\" . $class;
                $template->structure = (new $class)::execute();
            });

        return $template;
    }
}
