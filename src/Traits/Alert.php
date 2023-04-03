<?php

namespace ZapMe\Whmcs\Traits;

trait Alert
{
    public function success(string $message): string
    {
        return $this->alert($message);
    }

    protected function danger(string $message): string
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
