<?php
return [
    'paths' => ['api/*'], // Aplica CORS solo a rutas API
    'allowed_methods' => ['*'], // Permitir todos los métodos
    'allowed_origins' => ['http://34.55.61.47:81', 'http://localhost:5173'], // Solo un dominio
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Permitir todos los encabezados
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Solo si necesitas cookies o autenticación
];
