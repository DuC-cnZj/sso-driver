<?php

namespace DucCnzj\Sso;

class ApiTokenStorage implements TokenStorageImp
{
    public function get($name): ?string
    {
        return request()->headers->get($name);
    }

    public function set($name, $value)
    {
        request()->headers->set($name, $value);
//        response()->headers->set($name, $value);
    }

    public function has($name): bool
    {
        return request()->headers->has($name);
    }

    public function remove($name)
    {
        request()->headers->remove($name);
    }
}
