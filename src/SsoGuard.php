<?php

namespace DucCnzj\Sso;

use Illuminate\Support\Facades\Log;

class SsoGuard
{
    private $routeUserInfo = '/api/user/info';

    public function __invoke()
    {
        if ($token = session()->get('sso_token')) {
            try {
                $res = HttpClient::instance()->post($this->routeUserInfo, ['headers' => ['X-Request-Token' => $token]]);

                return new User(json_decode($res->getBody()->getContents())->data);
            } catch (\Throwable $e) {
                Log::error($e);

                return null;
            }
        }

        return null;
    }
}
