<?php

return [
    'base_url' => env('SSO_BASE_URL', 'http://localhost:8000'),
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_uri' => env('SSO_REDIRECT_URI', env('APP_URL') . '/sso/callback'),
    'authorize_endpoint' => env('SSO_AUTHORIZE_ENDPOINT', '/oauth/authorize'),
    'token_endpoint' => env('SSO_TOKEN_ENDPOINT', '/oauth/token'),
    'userinfo_endpoint' => env('SSO_USERINFO_ENDPOINT', '/oauth/userinfo'),
    'scopes' => env('SSO_SCOPES', 'openid profile email'),
    'role_map' => [
        'superadmin' => 'superadmin',
        'admin' => 'admin',
        'teacher' => 'pembina',
        'student' => 'student',
    ],
];
