<?php

namespace DucCnzj\Sso\Middleware;

use Closure;
use DucCnzj\Sso\HttpClient;
use Illuminate\Http\Request;
use DucCnzj\Sso\TokenStorageImp;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;

class SsoAuthenticate implements AuthenticatesRequests
{
    /**
     * @var TokenStorageImp
     */
    private TokenStorageImp $storageImp;

    public function __construct(TokenStorageImp $storageImp)
    {
        $this->storageImp = $storageImp;
    }

    protected function redirectTo(Request $request)
    {
        return config('sso.base_url') . '/' . config('sso.login_url') . '?redirect_url=' . $request->url();
    }

    public function handle($request, Closure $next)
    {
        $this->authenticate($request, 'sso');

        return $next($request);
    }

    protected function authenticate(Request $request, $guard)
    {
        if (! $this->storageImp->has(config('sso.token_field')) && ($accessToken = $request->access_token)) {
            try {
                $res = HttpClient::instance()->request('POST', '/' . config('sso.access_url'), ['json' => ['access_token' => $accessToken]]);

                $this->storageImp->set(config('sso.token_field'), json_decode($res->getBody()->getContents())->api_token);
            } catch (GuzzleException $e) {
                if ($e instanceof ClientException) {
                    Log::error($e);
                    $this->unauthenticated($request, [$guard]);
                }

                throw $e;
            }
        }

        if (auth($guard)->check()) {
            return auth()->shouldUse($guard);
        }

        $this->storageImp->remove(config('sso.token_field'));

        $this->unauthenticated($request, [$guard]);
    }

    protected function unauthenticated(Request $request, array $guards)
    {
        $ssoUrl = config('sso.base_url') . '/' . config('sso.login_url');
        $message = $request->expectsJson() ? 'Unauthenticated. should redirect to ' . $ssoUrl : 'Unauthenticated. ';

        throw new AuthenticationException(
            $message,
            $guards,
            $this->redirectTo($request)
        );
    }
}
