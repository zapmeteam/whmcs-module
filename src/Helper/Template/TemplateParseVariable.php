<?php

namespace ZapMe\Whmcs\Helper\Template;

use WHMCS\Database\Capsule;
use Illuminate\Support\Carbon;
use ZapMe\Whmcs\DTO\TemplateDTO;
use Illuminate\Support\Collection;
use ZapMe\Whmcs\Traits\InteractWithCarbon;
use Symfony\Component\HttpFoundation\Request;

class TemplateParseVariable
{
    use InteractWithCarbon;

    public function __construct(
        protected TemplateDTO $template,
        protected ?Collection $client = null,
    ) {
        $this->carbon();
        $this->default();
    }

    private function default(): void
    {
        $client        = $this->client->get('whmcs');
        $request       = Request::createFromGlobals();
        $configuration = Capsule::table('tblconfiguration')->whereIn('setting', ['CompanyName', 'Domain', 'SystemURL'])->get();

        $this->translate($client);

        $this->template->message = str_replace('%name%', $client->fullname, $this->template->message);
        $this->template->message = str_replace('%firstname%', $client->firstname, $this->template->message);
        $this->template->message = str_replace('%lastname%', $client->lastname, $this->template->message);
        $this->template->message = str_replace('%email%', $client->email, $this->template->message);
        $this->template->message = str_replace('%company%', $client->companyName, $this->template->message);
        $this->template->message = str_replace('%website%', $configuration->firstWhere('setting', '=', 'Domain')->value, $this->template->message);
        $this->template->message = str_replace('%companyname%', $configuration->firstWhere('setting', '=', 'CompanyName')->value, $this->template->message);
        $this->template->message = str_replace('%whmcs%', $configuration->firstWhere('setting', '=', 'SystemURL')->value, $this->template->message);
        $this->template->message = str_replace('%ipaddr%', $request->getClientIp(), $this->template->message);
        $this->template->message = str_replace('%date%', $this->carbon->format('d/m/Y'), $this->template->message);
        $this->template->message = str_replace('%hour%', $this->carbon->format('H:i'), $this->template->message);
    }

    private function translate(object $client): void
    {
        if (empty($language = $client->language) || $language === 'portuguese-br') {
            return;
        }

        $file = ZAPME_MODULE_PATH . "/translations/$language.php";

        if (!file_exists($file)) {
            return;
        }

        $hook     = strtolower($this->template->code);
        $language = collect(require $file);

        if (!$language->has($hook)) {
            return;
        }

        $this->template->message = $language->get($hook, $this->template->message);
    }

    public function ticket(object $ticket, mixed $vars): self
    {
        $date = Carbon::parse($ticket->lastreply);

        $this->template->message = str_replace('%id%', $ticket->id, $this->template->message);
        $this->template->message = str_replace('%tid%', $ticket->tid, $this->template->message);
        $this->template->message = str_replace('%title%', $ticket->title, $this->template->message);
        $this->template->message = str_replace('%ticketdate%', $date->format('d/m/Y'), $this->template->message);
        $this->template->message = str_replace('%tickethour%', $date->format('H:i'), $this->template->message);
        $this->template->message = str_replace('%deppartment%', $vars['deptname'], $this->template->message);

        return $this;
    }

    public function invoice(object $invoice): self
    {
        $this->template->message = str_replace('%invoiceid%', $invoice->id, $this->template->message);
        $this->template->message = str_replace('%duedate%', Carbon::parse($invoice->duedate)->format('d/m/Y'), $this->template->message);
        $this->template->message = str_replace('%value%', format_number($invoice->total), $this->template->message);

        return $this;
    }

    public function service(object $service): self
    {
        $this->template->message = str_replace('%id%', $service->id, $this->template->message);
        $this->template->message = str_replace('%product%', $service->product->name, $this->template->message);
        $this->template->message = str_replace('%duedate%', Carbon::parse($service->nextduedate)->format('d/m/Y'), $this->template->message);
        $this->template->message = str_replace('%value%', format_number($service->amount), $this->template->message);
        $this->template->message = str_replace('%ip%', $service->dedicatedip, $this->template->message);
        $this->template->message = str_replace('%domain%', $service->domain, $this->template->message);
        $this->template->message = str_replace('%user%', $service->username, $this->template->message);
        $this->template->message = str_replace('%password%', decrypt($service->password), $this->template->message);

        return $this;
    }

    public function parsed(): TemplateDTO
    {
        return $this->template;
    }
}
