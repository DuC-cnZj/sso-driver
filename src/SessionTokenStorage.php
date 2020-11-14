<?php

namespace DucCnzj\Sso;

class SessionTokenStorage implements TokenStorageImp
{
    public function get($name): ?string
    {
        return session()->get($name);
    }

    public function set($name, $value)
    {
        session()->put($name, $value);
    }

    public function has($name): bool
    {
        return session()->has($name);
    }

    public function remove($name)
    {
        session()->forget($name);
    }
}
