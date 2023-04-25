<?php

namespace ZapMe\Whmcs\Module;

use Illuminate\Support\Str;
use WHMCS\Database\Capsule;
use Illuminate\Support\Carbon;
use ZapMe\Whmcs\DTO\TemplateDto;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use ZapMe\Whmcs\Helper\Template\TemplateParseVariable;

class Template
{
    public function __construct(
        protected ?string $code = null,
        protected ?Collection $template = null,
        protected ?PagHiper $paghiper = null
    ) {
        $this->template = Capsule::table('mod_zapme_templates')
            ->when($code && ctype_alpha($code), fn (Builder $query) => $query->where('code', '=', $code))
            ->when($code && ctype_digit($code), fn (Builder $query) => $query->where('id', '=', $code))
            ->get();

        $this->paghiper = new PagHiper();
    }

    public function dto(): Collection
    {
        return $this->template->transform(function (object $item) {
            $item = $this->structure($item);

            return (new TemplateDto(
                id: $item->id,
                name: $item->structure?->name ?? $item->code,
                code: $item->code,
                message: $item->message,
                isActive: $item->is_active == 1,
                structure: $item->structure,
                createdAt: Carbon::parse($item->created_at),
                updatedAt: Carbon::parse($item->updated_at),
            ));
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
                $template->structure = (new $class())::execute($this->paghiper->active());
            });

        return $template;
    }

    public function variables(TemplateDto $template): TemplateParseVariable
    {
        return new TemplateParseVariable($template);
    }
}
