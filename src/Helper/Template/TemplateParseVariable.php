<?php

namespace ZapMe\Whmcs\Helper\Template;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\DTO\TemplateDTO;
use Illuminate\Support\Collection;
use ZapMe\Whmcs\Traits\InteractWithCarbon;
use Symfony\Component\HttpFoundation\Request;

class TemplateParseVariable
{
    use InteractWithCarbon;

    public function __construct(
        protected TemplateDTO $template,
        protected Collection $client,
    ) {
        $this->template->message = $this->translate();

        $this->carbon();
        $this->default();
    }

    public function client(): self
    {
        $request       = Request::createFromGlobals();
        $configuration = Capsule::table('tblconfiguration')
            ->whereIn('setting', ['CompanyName', 'Domain', 'SystemURL'])
            ->get();

        $this->template->message = str_replace('%website%', $configuration->firstWhere('setting', '=', 'Domain')->value, $this->template->message);
        $this->template->message = str_replace('%companyname%', $configuration->firstWhere('setting', '=', 'CompanyName')->value, $this->template->message);
        $this->template->message = str_replace('%whmcs%', $configuration->firstWhere('setting', '=', 'SystemURL')->value, $this->template->message);
        $this->template->message = str_replace('%ipaddr%', $request->getClientIp(), $this->template->message);
        $this->template->message = str_replace('%date%', $this->carbon->format('d/m/Y'), $this->template->message);
        $this->template->message = str_replace('%hour%', $this->carbon->format('H:i'), $this->template->message);

        return $this;
    }

    public function ticket(object $ticket): self
    {
        // $this->ticket = $ticket;

        return $this;
    }

    public function service(object $service): self
    {
        // $this->service = $service;

        return $this;
    }

    public function product(object $product): self
    {
        // $this->product = $product;

        return $this;
    }

    public function parsed(): TemplateDTO
    {
        return $this->template;
    }

    private function default(): void
    {
        $client = $this->client->get('whmcs');

        $this->template->message = str_replace('%name%', $client->fullname, $this->template->message);
        $this->template->message = str_replace('%firstname%', $client->firstname, $this->template->message);
        $this->template->message = str_replace('%lastname%', $client->lastname, $this->template->message);
        $this->template->message = str_replace('%email%', $client->email, $this->template->message);
        $this->template->message = str_replace('%company%', $client->companyName, $this->template->message);
    }

    private function translate(): string
    {
        $default = $this->template->message;

        if (!file_exists(ZAPME_MODULE_PATH . '/language/messages.php')) {
            return $default;
        }

        include ZAPME_MODULE_PATH . '/language/messages.php';

        $hook = strtolower($this->template->code);

        if (!isset($language[$hook]) || $this->client->country === 'BR') {
            return $default;
        }

        return $language[$hook];
    }
}
