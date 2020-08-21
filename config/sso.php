<?php

return [
    'base_url'      => rtrim(env("SSO_BASE_URL"), '/'),
    'user_info'     => trim(env("USER_INFO_URL", '/api/user/info'), '/'),
    'access_url'    => trim(env("ACCESS_TOKEN_URL", '/access_token'), '/'),
    'login_url'     => trim(env("LOGIN_URL", '/login'), '/'),
    'session_field' => env("SESSION_FIELD", 'sso_token'),
];