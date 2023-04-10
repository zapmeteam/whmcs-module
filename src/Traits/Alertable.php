<?php

namespace ZapMe\Whmcs\Traits;

trait Alertable
{
    public function success(string $message): string
    {
        return $this->alert($message);
    }

    public function danger(string $message): string
    {
        return $this->alert($message, 'danger');
    }

    protected function alert(string $message, string $type = 'success'): string
    {
        $icon = $type === 'success' ? 'check-circle' :  'exclamation-circle';

        return "<div class=\"alert alert-{$type} text-center\">
                    <i class=\"fa fa-{$icon}\"></i>
                    {$message}
                </div>";
    }
}
