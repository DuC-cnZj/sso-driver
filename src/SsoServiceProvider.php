<?php

namespace DucCnzj\Sso;

use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use DucCnzj\Sso\Middleware\SsoAuthenticate;

class SsoServiceProvider extends ServiceProvider
{
    protected $middlewareAliases = [
        'sso.auth' => SsoAuthenticate::class,
    ];

    public function register()
    {
        config([
            'auth.guards.sso' => array_merge([
                'driver'   => 'sso',
                'provider' => null,
            ], config('auth.guards.sso', [])),
        ]);

        $this->app->singleton(TokenStorageImp::class, function () {
            switch (config('sso.mode')) {
                case 'api':
                    return new ApiTokenStorage();
                default:
                    return new SessionTokenStorage();
            }
        });

        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/sso.php', 'sso');
        }
    }

    public function boot()
    {
        $this->aliasMiddleware();
        $this->configureGuard();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/sso.php' => config_path('sso.php'),
            ], 'sso');
        }
    }

    protected function configureGuard()
    {
        Auth::resolved(function ($auth) {
            $auth->extend('sso', function ($app, $name, array $config) use ($auth) {
                return tap($this->createGuard($auth, $config), function ($guard) {
                    $this->app->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    protected function aliasMiddleware()
    {
        $router = $this->app['router'];

        foreach ($this->middlewareAliases as $alias => $middleware) {
            $router->aliasMiddleware($alias, $middleware);
        }
    }

    private function createGuard($auth, array $config)
    {
        return (new class extends RequestGuard {
            public function logout()
            {
                auth()->user()->logout();
            }
        })(
            new SsoGuard(app(TokenStorageImp::class)),
            $this->app['request'],
            $auth->createUserProvider()
        );
    }
}
