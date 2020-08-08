<?php

namespace Uco\Sso\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
            $res = Http::asJson()->post(config('sso.base_url') . '/access_token', ['access_token' => $accessToken]);

            if ($res->failed()) {
                $this->unauthenticated($request, [$guard]);
            }

            session()->put('sso_token', $res['api_token']);
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
