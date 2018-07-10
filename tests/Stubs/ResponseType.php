<?php

namespace Hivokas\LaravelPassportSocialGrant\Tests\Stubs;

use Zend\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Server\ResponseTypes\AbstractResponseType;

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
