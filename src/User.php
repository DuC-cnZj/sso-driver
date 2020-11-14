<?php

namespace DucCnzj\Sso;

use Illuminate\Contracts\Auth\Authenticatable;

class User implements Authenticatable
{
    public $id;
    public $name;
    public $email;
    public $roles;
    public $permissions;
    public $originalData;

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
        return optional($this->originalData)->{$this->getAuthIdentifierName()};
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

    public function logout()
    {
        if ($token = session()->get(config('sso.token_field'))) {
            HttpClient::instance()->post(config('sso.base_url') . '/' . config('sso.logout_url'), ['headers' => ['X-Request-Token' => $token]]);
        }
    }

    public function __get($name)
    {
        return data_get($this->originalData, $name, null);
    }
}
