<?php

namespace Uco\Sso;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;

class User implements Authenticatable
{
    private $id;
    private $email;
    private $lastLoginAt;
    private $originalData;

    public function __construct(\stdClass $data)
    {
        $this->id = $data->id;
        $this->email = $data->email;
        $this->lastLoginAt = $data->last_login_at ? Carbon::parse($data->last_login_at) : null;
        $this->originalData = $data;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
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
}
