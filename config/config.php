<?php
declare(strict_types=1);

use App\Core\Env;

return [
    'app' => [
        'url' => Env::get('APP_URL', 'http://localhost:8000'),
    ],
    'api' => [
        'public_url' => Env::get('API_PUBLIC_URL', 'http://localhost:8001'),
        'internal_url' => Env::get('API_INTERNAL_URL', 'http://localhost:8001'),
        'timeout_seconds' => (int)Env::get('API_TIMEOUT_SECONDS', '12'),
    ],
    'admin' => [
        'list_limit' => (int)Env::get('ADMIN_LIST_LIMIT', '10'),
    ]
];
