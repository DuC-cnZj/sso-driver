<?php

namespace Uco\Sso;

use Illuminate\Support\Facades\Http;

class SsoGuard
{
    private $baseUrl;

    private $routeUserInfo = '/user/info';

    public function __construct()
    {
        $this->baseUrl = config('sso.base_url');
    }

    public function __invoke()
    {
        if ($token = session()->get('sso_token')) {
            $res = Http::withHeaders(['X-Request-Token' => $token])->post($this->baseUrl . $this->routeUserInfo);
            if ($res->ok()) {
                return new User(json_decode($res->body())->data);
            }
        }

        return null;
    }
}
