<?php

namespace Hivokas\LaravelPassportSocialGrant\Tests\Stubs;

use League\OAuth2\Server\ResponseTypes\AbstractResponseType;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

class ResponseType extends AbstractResponseType
{
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    public function generateHttpResponse(ResponseInterface $response)
    {
        return new Response();
    }
}