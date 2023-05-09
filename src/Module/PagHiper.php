<?php

namespace ZapMe\Whmcs\Module;

use Exception;
use Illuminate\Support\Str;
use ZapMe\Whmcs\DTO\TemplateDto;

class PagHiper
{
    public function __construct(
        protected TemplateDto $template,
        protected object $client
    ) {
        //
    }

    public function generate(object $invoice): array
    {
        $attachment = $this->parse($invoice);

        return [
            $this->template->message,
            $attachment,
        ];
    }

    private function parse(object $invoice): array
    {
        $document = [];

        if (
            !paghiper_active() || !Str::of($invoice->paymentmethod)->contains('paghiper') || $invoice->total < 3.00
        ) {
            $this->extract();

            return $document;
        }

        $whmcsurl = rtrim(\App::getSystemUrl(), "/");
        $billet   = json_decode(file_get_contents($whmcsurl . '/modules/gateways/paghiper.php?invoiceid=' . $invoice->id . '&uuid=' . $this->client->id . '&mail=' . $this->client->email . '&json=1'), true);

        $code = $billet['bank_slip']['digitable_line'] ?? null;
        $pdf  = $billet['bank_slip']['url_slip_pdf']   ?? null;

        if (!$code || !$pdf) {
            $this->extract();

            return $document;
        }

        $this->template->message = str_replace('%paghiper_codigo%', $code, $this->template->message);

        if (($generate = Str::of($this->template->message)->contains('%paghiper_boleto%')) === false) {
            $this->extract('boleto');

            return $document;
        }

        $billet = $generate ? $this->billet($pdf) : null;

        if ($billet) {
            $this->extract('boleto');

            $document = ['file_content' => $billet, 'file_extension' => 'pdf'];
        }

        return $document;
    }

    private function billet(string $link): ?string
    {
        $billet = null;

        try {
            $billet = base64_encode(file_get_contents($link));
        } catch (Exception $e) {
            throwlable($e);
        }

        return $billet;
    }

    private function extract(string $what = null): void
    {
        $words = $what ? ["%paghiper_{$what}%"] : ['%paghiper_codigo%', '%paghiper_boleto%'];

        $this->template->message = str_replace($words, '', $this->template->message);
    }
}
