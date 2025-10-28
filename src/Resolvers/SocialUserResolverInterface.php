<?php

namespace Coderello\SocialGrant\Resolvers;

use Illuminate\Contracts\Auth\Authenticatable;
use League\OAuth2\Server\Entities\ClientEntityInterface;

interface SocialUserResolverInterface
{
    /**
     * Resolve user by provider credentials.
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken, ClientEntityInterface $client): ?Authenticatable;
}
