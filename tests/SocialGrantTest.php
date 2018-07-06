<?php

namespace Hivokas\LaravelPassportSocialGrant\Tests;

use Hivokas\LaravelPassportSocialGrant\Grants\SocialGrant;
use Hivokas\LaravelPassportSocialGrant\Resolvers\SocialUserResolverInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use PHPUnit\Framework\TestCase;

class SocialGrantTest extends TestCase
{
    public function test_get_identifier()
    {
        $socialUserResolverMock = $this->getMockBuilder(SocialUserResolverInterface::class)->getMock();
        $refreshTokenRepositoryMock = $this->getMockBuilder(RefreshTokenRepositoryInterface::class)->getMock();

        $socialGrant = new SocialGrant($socialUserResolverMock, $refreshTokenRepositoryMock);
        $this->assertEquals('social', $socialGrant->getIdentifier());
    }
}