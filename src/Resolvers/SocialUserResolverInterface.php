<?php

namespace Coderello\SocialGrant\Resolvers;

use Illuminate\Contracts\Auth\Authenticatable;

interface SocialUserResolverInterface
{
    /**
     * Resolve user by provider credentials.
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable;
}
