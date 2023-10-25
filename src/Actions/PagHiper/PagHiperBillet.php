<?php

namespace ZapMe\Whmcs\Actions\PagHiper;

use App;
use Exception;
use ZapMe\Whmcs\DTO\TemplateDto;

class PagHiperBillet
{
    /** @var TemplateDto */
    protected $template;

    /** @var object */
    protected $client;

    public function __construct(TemplateDto $template, object $client)
    {
        $this->template = $template;
        $this->client   = $client;
    }

    public static function execute(TemplateDto $template, object $client, object $invoice): array
    {
        $class      = new static($template, $client);
        $attachment = $class->parse($invoice);

        return [
            $class->template->message,
            $attachment,
        ];
    }

    private function parse(object $invoice): array
    {
        if (!paghiper_active() || $invoice->paymentmethod !== 'paghiper' || $invoice->total < 3.00) {
            $this->erase();

            return [];
        }

        [$code, $pdf] = $this->extract($invoice);

        if (!$code || !$pdf) {
            $this->erase();

            return [];
        }

        $this->template->message = str_replace('%paghiper_codigo%', $code, $this->template->message);

        if (strpos($this->template->message, '%paghiper_boleto%') === false) {
            $this->erase('boleto');

            return [];
        }

        $billet = $this->pdf($pdf);

        if (!$billet) {
            return [];
        }

        $this->erase('boleto');

        return [
            'file_content'   => $billet,
            'file_extension' => 'pdf'
        ];
    }

    private function extract(object $invoice): array
    {
        try {
            $whmcs  = rtrim(App::getSystemUrl(), "/");
            $billet = json_decode(file_get_contents($whmcs . '/modules/gateways/paghiper.php?invoiceid=' . $invoice->id . '&uuid=' . $this->client->id . '&mail=' . $this->client->email . '&json=1'), true);
        } catch (Exception $e) {
            throwlable($e);
        }

        return [
            $billet['bank_slip']['digitable_line'] ?? $billet['digitable_line'] ?? null,
            $billet['bank_slip']['url_slip_pdf']   ?? $billet['url_slip_pdf'] ?? null,
        ];
    }

    private function pdf(string $link): ?string
    {
        $billet = null;

        try {
            $billet = base64_encode(file_get_contents($link));
        } catch (Exception $e) {
            throwlable($e);
        }

        return $billet;
    }

    private function erase(?string $specific = null): void
    {
        $words = $specific ? ["%paghiper_{$specific}%"] : ['%paghiper_codigo%', '%paghiper_boleto%'];

        $this->template->message = str_replace($words, '', $this->template->message);
    }
}
