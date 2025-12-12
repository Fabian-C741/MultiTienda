<?php

declare(strict_types=1);

return [
    'central_domains' => array_filter(array_map('trim', explode(',', env('CENTRAL_DOMAINS', 'admin.localhost')))),

    'path_identifier' => env('TENANT_PATH_IDENTIFIER', 'tenant'),
];
