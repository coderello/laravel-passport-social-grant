<?php

namespace Coderello\SocialGrant\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Coderello\SocialGrant\Grants\SocialGrant;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;

class SocialGrantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->resolving(AuthorizationServer::class, function (AuthorizationServer $server) {
            $server->enableGrantType(
                $this->makeSocialGrant(),
                Passport::tokensExpireIn()
            );
        });
    }

    protected function makeSocialGrant(): SocialGrant
    {
        $grant = new SocialGrant(
            $this->app->make(SocialUserResolverInterface::class),
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}