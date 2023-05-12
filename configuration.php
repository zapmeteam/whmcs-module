<?php

return [
    'zapme_api_url'             => $_ENV['ZAPME_MODULE_API_URL']            ?? 'https://api.zapme.com.br',
    'zapme_api_key'             => $_ENV['ZAPME_MODULE_API_KEY']            ?? '',
    'zapme_api_secret'          => $_ENV['ZAPME_MODULE_API_SECRET']         ?? '',
    'zapme_module_activity_log' => (bool)$_ENV['ZAPME_MODULE_ACTIVITY_LOG'] ?? true,
];
