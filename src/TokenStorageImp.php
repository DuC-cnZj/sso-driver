<?php

namespace DucCnzj\Sso;

interface TokenStorageImp
{
    public function get($name): ?string ;

    public function set($name, $value);

    public function has($name): bool ;

    public function remove($name);
}
