<?php

namespace ZapMe\Whmcs\Helper\Template;

use WHMCS\Database\Capsule;
use Illuminate\Support\Carbon;
use ZapMe\Whmcs\DTO\TemplateDTO;
use ZapMe\Whmcs\Traits\InteractWithCarbon;
use Symfony\Component\HttpFoundation\Request;

class TemplateParseVariable
{
    use InteractWithCarbon;

    protected object $client;
    protected object $ticket;
    protected object $service;
    protected object $product;

    public function __construct(
        protected TemplateDTO $template
    ) {
        $this->template->message = $this->translate();

        $this->newCarbonInstance();
    }

    public function client(object $client): self
    {
        $this->client  = $client;
        $request       = Request::createFromGlobals();
        $configuration = Capsule::table('tblconfiguration')
            ->whereIn('setting', ['CompanyName', 'Domain', 'SystemURL'])
            ->get();

        $this->default();

        $this->template->message = str_replace('%website%', $configuration->firstWhere('setting', '=', 'Domain')->value, $this->template->message);
        $this->template->message = str_replace('%companyname%', $configuration->firstWhere('setting', '=', 'CompanyName')->value, $this->template->message);
        $this->template->message = str_replace('%whmcs%', $configuration->firstWhere('setting', '=', 'SystemURL')->value, $this->template->message);
        $this->template->message = str_replace('%ipaddr%', $request->getClientIp(), $this->template->message);
        $this->template->message = str_replace('%date%', $this->carbon->format('d/m/Y'), $this->template->message);
        $this->template->message = str_replace('%hour%', $this->carbon->format('H:i'), $this->template->message);

        return $this;
    }

    public function fromTicket(object $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function fromService(object $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function fromProduct(object $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function get(): TemplateDTO
    {
        return $this->template;
    }

    private function default(): void
    {
        $this->template->message = str_replace('%name%', $this->client->fullname, $this->template->message);
        $this->template->message = str_replace('%firstname%', $this->client->firstname, $this->template->message);
        $this->template->message = str_replace('%lastname%', $this->client->lastname, $this->template->message);
        $this->template->message = str_replace('%email%', $this->client->email, $this->template->message);
        $this->template->message = str_replace('%company%', $this->client->companyName, $this->template->message);
    }

    private function translate(): string
    {
        if (!file_exists(ZAPME_MODULE_PATH . '/language/messages.php')) {
            return $this->template->message;
        }

        include ZAPME_MODULE_PATH . '/language/messages.php';

        $hook = strtolower($this->template->code);

        if (!isset($language[$hook]) || $this->client->country === 'BR') {
            return $this->template->message;
        }

        return $language[$hook];
    }
}
