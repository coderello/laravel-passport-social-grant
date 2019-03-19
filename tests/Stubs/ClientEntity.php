<?php

namespace Coderello\SocialGrant\Tests\Stubs;

use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\ClientEntityInterface;

class ClientEntity implements ClientEntityInterface
{
    use EntityTrait, ClientTrait;

    public function setRedirectUri($uri)
    {
        $this->redirectUri = $uri;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
