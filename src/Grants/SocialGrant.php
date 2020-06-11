<?php

namespace Coderello\SocialGrant\Grants;

use League\OAuth2\Server\RequestEvent;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Grant\AbstractGrant;
use Laravel\Passport\Bridge\User as UserEntity;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;

class SocialGrant extends AbstractGrant
{
    /**
     * Social user resolver instance.
     *
     * @var SocialUserResolverInterface
     */
    protected $resolver;

    /**
     * SocialGrant constructor.
     *
     * @param SocialUserResolverInterface $resolver
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function __construct(
        SocialUserResolverInterface $resolver,
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->resolver = $resolver;
        $this->setRefreshTokenRepository($refreshTokenRepository);

        $this->refreshTokenTTL = new \DateInterval('P1M');
    }

    /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        \DateInterval $accessTokenTTL
    ): ResponseTypeInterface {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request, $this->defaultScope));
        $user = $this->validateUser($request);

        // Finalize the requested scopes
        $finalizedScopes = $this->scopeRepository->finalizeScopes($scopes, 'password', $client, $user->getIdentifier());

        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $finalizedScopes);
        $refreshToken = $this->issueRefreshToken($accessToken);

        // Send events to emitter
        $this->getEmitter()->emit(new RequestEvent(RequestEvent::ACCESS_TOKEN_ISSUED, $request));
        $this->getEmitter()->emit(new RequestEvent(RequestEvent::REFRESH_TOKEN_ISSUED, $request));

        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        return $responseType;
    }

    /**
     * Validate server request and get the user entity.
     *
     * @param ServerRequestInterface $request
     *
     * @throw OAuthServerException
     *
     * @return UserEntity
     */
    public function validateUser(ServerRequestInterface $request): UserEntity
    {
        $provider = $this->getRequestParameter('provider', $request);
        if (is_null($provider)) {
            throw OAuthServerException::invalidRequest('provider');
        }

        $accessToken = $this->getRequestParameter('access_token', $request);
        if (is_null($accessToken)) {
            throw OAuthServerException::invalidRequest('access_token');
        }

        $user = $this->resolver->resolveUserByProviderCredentials($provider, $accessToken);
        if (is_null($user)) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));
            throw OAuthServerException::invalidCredentials();
        }

        return new UserEntity($user->getAuthIdentifier());
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return 'social';
    }
}
