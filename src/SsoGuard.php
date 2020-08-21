<?php

namespace DucCnzj\Sso;

class SsoGuard
{
    private $routeUserInfo = '/api/user/info';

    public function __invoke()
    {
        if ($token = session()->get('sso_token')) {
            $res = Http::instance()->post($this->routeUserInfo, ['headers' => ['X-Request-Token' => $token]]);

            if ($res->getStatusCode() == 200) {
                return new User(json_decode($res->body())->data);
            }
        }

        return null;
    }
}
