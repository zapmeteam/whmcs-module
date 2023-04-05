<?php

namespace ZapMe\Whmcs\Helper\Template;

abstract class AbstractTemplateStructure
{
    public static function execute(bool $paghiper): object
    {
        $variables = (new static())->variables();

        return (object) array_merge(
            (new static())->base(),
            ['rules'     => (new static())->rules()],
            ['variables' => $variables],
            ['print'     => (new static())->print($paghiper, $variables)],
        );
    }

    public function print(bool $paghiper, array $variables): string
    {
        $alert  = '<div class="alert alert-info text-center">';
        $alert .= '%name% - Nome do Cliente (completo)<br>';
        $alert .= '%firstname% - Primeiro Nome do Cliente<br>';
        $alert .= '%lastname% - Último Nome do Cliente<br>';
        $alert .= '%email% - E-mail do Cliente<br>';
        $alert .= '%company% - Compania do Cliente<br>';

        foreach ($variables as $key => $value) {
            if (!$paghiper && mb_strpos($key, 'paghiper')) {
                continue;
            }

            $alert .= '%' . $key . '% - ' . $value . '<br>';
        }

        $alert .= '</div>';

        return $alert;
    }
}
