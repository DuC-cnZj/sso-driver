<?php

namespace DucCnzj\Sso;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class SsoGuard
{
    public function __invoke()
    {
        if ($token = session()->get(config('sso.session_field'))) {
            try {
                $res = HttpClient::instance()->post('/' . sprintf(config("sso.user_info"), config("sso.project")), ['headers' => ['X-Request-Token' => $token]]);

                return new User(json_decode($res->getBody()->getContents())->data);
            } catch (\Throwable $e) {
                Log::error($e);

                if ($e instanceof ClientException && $e->getCode() == 401) {
                    return null;
                }

                throw $e;
            }
        }

        return null;
    }
}
