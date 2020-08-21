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
        return config('sso.base_url') . '/' . config('sso.login_url') . '?redirect_url=' . $request->url();
    }

    public function handle($request, Closure $next)
    {
        $this->authenticate($request, 'sso');

        return $next($request);
    }

    protected function authenticate(Request $request, $guard)
    {
        if (!session()->has(config('sso.session_field')) && ($accessToken = $request->access_token)) {
            try {
                $res = HttpClient::instance()->request('POST', '/' . config('sso.access_url'), ['json' => ['access_token' => $accessToken]]);

                session()->put(config('sso.session_field'), json_decode($res->getBody()->getContents())->api_token);
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

        session()->forget(config('sso.session_field'));

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
