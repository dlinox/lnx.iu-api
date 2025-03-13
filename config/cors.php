<?php
return [
    'paths' => ['api/*'], // Aplica CORS solo a rutas API
    'allowed_methods' => ['*'], // Permitir todos los métodos
    'allowed_origins' => [
        'http://34.55.61.47:81',
        'http://localhost:2025',
        'http://localhost:2024',
        'http://localhost:2023',
        'http://34.55.61.47:83'
    ], // Solo estos orígenes pueden acceder a la API
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Permitir todos los encabezados
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Solo si necesitas cookies o autenticación
];
