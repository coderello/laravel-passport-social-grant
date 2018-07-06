<?php

namespace Hivokas\LaravelPassportSocialGrant\Tests;

use Hivokas\LaravelPassportSocialGrant\Grants\SocialGrant;
use Hivokas\LaravelPassportSocialGrant\Resolvers\SocialUserResolverInterface;
use Hivokas\LaravelPassportSocialGrant\Tests\Stubs\AccessTokenEntity;
use Hivokas\LaravelPassportSocialGrant\Tests\Stubs\ClientEntity;
use Hivokas\LaravelPassportSocialGrant\Tests\Stubs\RefreshTokenEntity;
use Hivokas\LaravelPassportSocialGrant\Tests\Stubs\ScopeEntity;
use Hivokas\LaravelPassportSocialGrant\Tests\Stubs\ResponseType;
use Hivokas\LaravelPassportSocialGrant\Tests\Stubs\User;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;

class SocialGrantTest extends TestCase
{
    const DEFAULT_SCOPE = 'default_scope';

    public function test_get_identifier()
    {
        $socialUserResolverMock = $this->getMockBuilder(SocialUserResolverInterface::class)->getMock();
        $refreshTokenRepositoryMock = $this->getMockBuilder(RefreshTokenRepositoryInterface::class)->getMock();

        $socialGrant = new SocialGrant($socialUserResolverMock, $refreshTokenRepositoryMock);
        $this->assertEquals('social', $socialGrant->getIdentifier());
    }

    public function test_respond_to_request()
    {
        $client = new ClientEntity();
        $clientRepositoryMock = $this->getMockBuilder(ClientRepositoryInterface::class)->getMock();
        $clientRepositoryMock->method('getClientEntity')->willReturn($client);

        $accessTokenRepositoryMock = $this->getMockBuilder(AccessTokenRepositoryInterface::class)->getMock();
        $refreshTokenEntity = new AccessTokenEntity();
        $accessTokenRepositoryMock->method('getNewToken')->willReturn($refreshTokenEntity);
        $accessTokenRepositoryMock->method('persistNewAccessToken')->willReturnSelf();

        $socialUserResolverMock = $this->getMockBuilder(SocialUserResolverInterface::class)->getMock();
        $user = new User();
        $socialUserResolverMock->method('resolveUserByProviderCredentials')->willReturn($user);

        $refreshTokenRepositoryMock = $this->getMockBuilder(RefreshTokenRepositoryInterface::class)->getMock();
        $refreshTokenRepositoryMock->method('persistNewRefreshToken')->willReturnSelf();
        $refreshTokenEntity = new RefreshTokenEntity();
        $refreshTokenRepositoryMock->method('getNewRefreshToken')->willReturn($refreshTokenEntity);

        $scope = new ScopeEntity();
        $scopeRepositoryMock = $this->getMockBuilder(ScopeRepositoryInterface::class)->getMock();
        $scopeRepositoryMock->method('getScopeEntityByIdentifier')->willReturn($scope);
        $scopeRepositoryMock->method('finalizeScopes')->willReturnArgument(0);

        $grant = new SocialGrant($socialUserResolverMock, $refreshTokenRepositoryMock);
        $grant->setClientRepository($clientRepositoryMock);
        $grant->setAccessTokenRepository($accessTokenRepositoryMock);
        $grant->setScopeRepository($scopeRepositoryMock);
        $grant->setDefaultScope(self::DEFAULT_SCOPE);

        $serverRequest = new ServerRequest();
        $serverRequest = $serverRequest->withParsedBody(
            [
                'client_id' => 'client_id_value',
                'client_secret' => 'client_secret_value',
                'provider' => 'provider_value',
                'access_token' => 'access_token_value',
            ]
        );

        $responseType = new ResponseType();
        $grant->respondToAccessTokenRequest($serverRequest, $responseType, new \DateInterval('PT5M'));

        $this->assertInstanceOf(AccessTokenEntityInterface::class, $responseType->getAccessToken());
        $this->assertInstanceOf(RefreshTokenEntityInterface::class, $responseType->getRefreshToken());
    }

    public function test_respond_to_request_missing_provider()
    {
        $this->expectException(OAuthServerException::class);

        $client = new ClientEntity();
        $clientRepositoryMock = $this->getMockBuilder(ClientRepositoryInterface::class)->getMock();
        $clientRepositoryMock->method('getClientEntity')->willReturn($client);

        $accessTokenRepositoryMock = $this->getMockBuilder(AccessTokenRepositoryInterface::class)->getMock();

        $socialUserResolverMock = $this->getMockBuilder(SocialUserResolverInterface::class)->getMock();

        $refreshTokenRepositoryMock = $this->getMockBuilder(RefreshTokenRepositoryInterface::class)->getMock();

        $grant = new SocialGrant($socialUserResolverMock, $refreshTokenRepositoryMock);
        $grant->setClientRepository($clientRepositoryMock);
        $grant->setAccessTokenRepository($accessTokenRepositoryMock);

        $serverRequest = new ServerRequest();
        $serverRequest = $serverRequest->withParsedBody(
            [
                'client_id' => 'client_id_value',
                'client_secret' => 'client_secret_value',
                'access_token' => 'access_token_value',
            ]
        );

        $responseType = new ResponseType();
        $grant->respondToAccessTokenRequest($serverRequest, $responseType, new \DateInterval('PT5M'));
    }

    public function test_respond_to_request_missing_access_token()
    {
        $this->expectException(OAuthServerException::class);

        $client = new ClientEntity();
        $clientRepositoryMock = $this->getMockBuilder(ClientRepositoryInterface::class)->getMock();
        $clientRepositoryMock->method('getClientEntity')->willReturn($client);

        $accessTokenRepositoryMock = $this->getMockBuilder(AccessTokenRepositoryInterface::class)->getMock();

        $socialUserResolverMock = $this->getMockBuilder(SocialUserResolverInterface::class)->getMock();

        $refreshTokenRepositoryMock = $this->getMockBuilder(RefreshTokenRepositoryInterface::class)->getMock();

        $grant = new SocialGrant($socialUserResolverMock, $refreshTokenRepositoryMock);
        $grant->setClientRepository($clientRepositoryMock);
        $grant->setAccessTokenRepository($accessTokenRepositoryMock);

        $serverRequest = new ServerRequest();
        $serverRequest = $serverRequest->withParsedBody(
            [
                'client_id' => 'client_id_value',
                'client_secret' => 'client_secret_value',
                'provider' => 'provider_value',
            ]
        );

        $responseType = new ResponseType();
        $grant->respondToAccessTokenRequest($serverRequest, $responseType, new \DateInterval('PT5M'));
    }

    public function test_respond_to_bad_credentials()
    {
        $this->expectException(OAuthServerException::class);

        $client = new ClientEntity();
        $clientRepositoryMock = $this->getMockBuilder(ClientRepositoryInterface::class)->getMock();
        $clientRepositoryMock->method('getClientEntity')->willReturn($client);

        $accessTokenRepositoryMock = $this->getMockBuilder(AccessTokenRepositoryInterface::class)->getMock();

        $socialUserResolverMock = $this->getMockBuilder(SocialUserResolverInterface::class)->getMock();
        $user = null;
        $socialUserResolverMock->method('resolveUserByProviderCredentials')->willReturn($user);

        $refreshTokenRepositoryMock = $this->getMockBuilder(RefreshTokenRepositoryInterface::class)->getMock();

        $grant = new SocialGrant($socialUserResolverMock, $refreshTokenRepositoryMock);
        $grant->setClientRepository($clientRepositoryMock);
        $grant->setAccessTokenRepository($accessTokenRepositoryMock);

        $serverRequest = new ServerRequest();
        $serverRequest = $serverRequest->withParsedBody(
            [
                'client_id' => 'client_id_value',
                'client_secret' => 'client_secret_value',
                'provider' => 'provider_value',
                'access_token' => 'access_token_value',
            ]
        );

        $responseType = new ResponseType();
        $grant->respondToAccessTokenRequest($serverRequest, $responseType, new \DateInterval('PT5M'));
    }
}