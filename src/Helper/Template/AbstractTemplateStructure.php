<?php

namespace ZapMe\Whmcs\Helper\Template;

abstract class AbstractTemplateStructure
{
    public static function execute(bool $paghiper): object
    {
        $class     = new static();
        $variables = $class->variables();

        return (object)array_merge(
            $class->base(),
            ['print'     => $class->print($paghiper, $variables)],
            ['variables' => $variables],
        );
    }

    public function print(bool $paghiper, array $variables): string
    {
        $alert = "<div class=\"alert alert-info text-center\">";
        $alert .= "<b>%name%:</b> Nome do Cliente (completo)<br>";
        $alert .= "<b>%firstname%:</b> Primeiro Nome do Cliente<br>";
        $alert .= "<b>%lastname%:</b> Último Nome do Cliente<br>";
        $alert .= "<b>%email%:</b> E-mail do Cliente<br>";
        $alert .= "<b>%company%:</b> Compania do Cliente<br>";
        $alert .= "<b>%website%:</b> Domínio do WHMCS<br>";
        $alert .= "<b>%companyname%:</b> Nome da Empresa<br>";
        $alert .= "<b>%whmcs%:</b> Link do WHMCS<br>";
        $alert .= "<b>%ipaddr%:</b> IP da Requisição<br>";
        $alert .= "<b>%date%:</b> Data (d/m/Y)<br>";
        $alert .= "<b>%hour%:</b> Data (H:m)<br>";

        if (count($variables) > 0) {
            $alert .= "<hr>";
        }

        foreach ($variables as $key => $value) {
            if (!$paghiper && mb_strpos($key, 'paghiper')) {
                continue;
            }

            $alert .= "<b>%$key%:</b> $value<br>";
        }

        $alert .= '</div>';

        return $alert;
    }
}
