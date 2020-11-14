<?php

namespace DucCnzj\Sso;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;

class SsoGuard
{
    /**
     * @var TokenStorageImp
     */
    private TokenStorageImp $storageImp;

    public function __construct(TokenStorageImp $storageImp)
    {
        $this->storageImp = $storageImp;
    }

    public function __invoke()
    {
        if ($token = $this->storageImp->get(config('sso.token_field'))) {
            try {
                $res = HttpClient::instance()->post('/' . sprintf(config('sso.user_info'), config('sso.project')), ['headers' => ['X-Request-Token' => $token]]);

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
