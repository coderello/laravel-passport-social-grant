<?php

namespace Hivokas\LaravelPassportSocialGrant\Providers;

use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use Hivokas\LaravelPassportSocialGrant\Grants\SocialGrant;
use Hivokas\LaravelPassportSocialGrant\Resolvers\SocialUserResolverInterface;

class SocialGrantServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->resolving(AuthorizationServer::class, function (AuthorizationServer $server) {
            $server->enableGrantType(
                $this->makeSocialGrant(),
                Passport::tokensExpireIn()
            );
        });
    }

    /**
     * Create and configure a Social grant instance.
     *
     * @return SocialGrant
     */
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
