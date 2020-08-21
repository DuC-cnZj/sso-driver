<?php

namespace DucCnzj\Sso;

use Illuminate\Support\Facades\Log;

class SsoGuard
{
    public function __invoke()
    {
        if ($token = session()->get(config('sso.session_field'))) {
            try {
                $res = HttpClient::instance()->post('/' . config("sso.user_info"), ['headers' => ['X-Request-Token' => $token]]);

                return new User(json_decode($res->getBody()->getContents())->data);
            } catch (\Throwable $e) {
                Log::error($e);

                return null;
            }
        }

        return null;
    }
}
