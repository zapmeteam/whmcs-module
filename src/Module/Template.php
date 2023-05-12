<?php

namespace ZapMe\Whmcs\Module;

use Illuminate\Support\Str;
use WHMCS\Database\Capsule;
use ZapMe\Whmcs\DTO\TemplateDto;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use ZapMe\Whmcs\Helper\Template\TemplateParseVariable;

class Template
{
    /** @var string|null */
    protected $code = null;

    /** @var Collection|null */
    protected $template = null;

    public function __construct(?string $code = null)
    {
        $this->template = Capsule::table('mod_zapme_templates')
            ->when($code && ctype_alpha($code), function (Builder $query) use ($code) {
                return $query->where('code', '=', $code);
            })
            ->when($code && ctype_digit($code), function (Builder $query) use ($code) {
                return $query->where('id', '=', $code);
            })
            ->get();
    }

    public function dto(): Collection
    {
        return $this->template->transform(function (object $item) {
            $item = $this->structure($item);

            return (new TemplateDto(
                $item->id,
                $item->structure->name ?? $item->code,
                $item->code,
                $item->message,
                $item->is_active == 1,
                $item->structure,
                now()->parse($item->created_at),
                now()->parse($item->updated_at),
            ));
        });
    }

    private function structure(object $template): object
    {
        collect(glob(ZAPME_MODULE_PATH . "/src/Helper/Template/Structures/*.php"))
            ->filter(function (string $file) use ($template) {
                Str::of($file)->contains($template->code);
            })
            ->each(function (string $file) use (&$template) {
                $class = Str::of($file)
                    ->afterLast('/')
                    ->beforeLast('.php')
                    ->__toString();

                $class               = "ZapMe\\Whmcs\\Helper\\Template\\Structures\\" . $class;
                $template->structure = (new $class())::execute();
            });

        return $template;
    }

    public function variables(TemplateDto $template): TemplateParseVariable
    {
        return new TemplateParseVariable($template);
    }
}
