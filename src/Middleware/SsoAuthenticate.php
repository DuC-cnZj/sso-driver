<?php

namespace DucCnzj\Sso\Middleware;

use Closure;
use DucCnzj\Sso\HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;

class SsoAuthenticate implements AuthenticatesRequests
{
    protected function redirectTo(Request $request)
    {
        return config('sso.base_url') . '/login?redirect_url=' . $request->url();
    }

    public function handle($request, Closure $next)
    {
        $this->authenticate($request, 'sso');

        return $next($request);
    }

    protected function authenticate(Request $request, $guard)
    {
        if (! session()->has('sso_token') && ($accessToken = $request->access_token)) {
            try {
                $res = HttpClient::instance()->request('POST', '/access_token', ['json' => ['access_token' => $accessToken]]);

                session()->put('sso_token', json_decode($res->getBody()->getContents())->api_token);
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

        session()->forget('sso_token');

        $this->unauthenticated($request, [$guard]);
    }

    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            $this->redirectTo($request)
        );
    }
}
