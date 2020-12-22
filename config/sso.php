<?php

return [
    'project'     => env("SSO_PROJECT_NAME", env("APP_NAME")),
    'base_url'    => trim(env("SSO_BASE_URL"), '/'),
    'user_info'   => trim(env("USER_INFO_URL", '/api/user/info/projects/%s'), '/'),
    'logout_url'  => trim(env("SSO_LOGOUT_URL", '/api/logout'), '/'),
    'access_url'  => trim(env("ACCESS_TOKEN_URL", '/access_token'), '/'),
    'login_url'   => trim(env("LOGIN_URL", '/login'), '/'),
    'token_field' => env("TOKEN_FIELD", 'X-Stream-ID'),

    /**
     * mode: web or api.
     */
    'mode'        => env("SSO_MODE", 'web'),
];