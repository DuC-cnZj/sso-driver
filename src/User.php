<?php

namespace DucCnzj\Sso;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;

class User implements Authenticatable
{
    private $id;
    private $name;
    private $email;
    private $roles;
    private $permissions;
    private $originalData;

    public function __construct(\stdClass $data)
    {
        $this->id = data_get($data, 'id', null);
        $this->name = data_get($data, 'user_name', null);
        $this->email = data_get($data, 'email', null);
        $this->roles = data_get($data, 'roles', []);
        $this->permissions = data_get($data, 'permissions', []);
        $this->originalData = $data;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function roles()
    {
        return $this->roles;
    }

    public function permissions()
    {
        return collect($this->permissions)
            ->filter(function ($item) {
                return data_get($item, 'project') == config('sso.project');
            })
            ->pluck('name')
            ->values()
            ->all();
    }

    public function hasRole($name)
    {
        return in_array($name, $this->roles);
    }

    public function hasPermission($name)
    {
        return collect($this->permissions)
            ->filter(function ($item) {
                return data_get($item, 'project') == config('sso.project');
            })
            ->pluck('name')
            ->contains($name);
    }

    public function getAuthIdentifier()
    {
        return optional($this->originalData)->id;
    }

    public function getAuthPassword()
    {
    }

    public function getRememberToken()
    {
    }

    public function setRememberToken($value)
    {
    }

    public function getRememberTokenName()
    {
    }

    public function __get($name)
    {
        return data_get($this->originalData, $name, null);
    }
}
