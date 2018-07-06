<?php

namespace Hivokas\LaravelPassportSocialGrant\Tests\Stubs;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class User implements Authenticatable
{
    use AuthenticatableTrait;

    protected $rememberToken = 'remember_token';

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return 1;
    }

    public function getAuthPassword()
    {
        return 'password';
    }

    public function getRememberToken()
    {
        return $this->rememberToken;
    }

    public function setRememberToken($value)
    {
        $this->rememberToken = $value;
    }
}