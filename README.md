# Laravel Passport Social Grant

This package adds a social grant for your OAuth2 server. It can be useful if have an API and want to provide the ability for your users to login/register through social networks.

As a result you will be able to exchange `access_token`, issued by the OAuth2 server of any social provider, to `access_token` and `refresh_token` issued by your own OAuth2 server. You will receive this `access_token` and return the user instance that corresponds to it on your own.

## Installation

You can install this package via composer using this command:

```bash
composer require coderello/laravel-passport-social-grant
```

The package will automatically register itself.

## Configuring

As the first step, you need to implement `SocialUserResolverInterface`:
```php
<?php

namespace App\Resolvers;

use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Facades\Socialite;

class SocialUserResolver implements SocialUserResolverInterface
{
    /**
     * Resolve user by provider credentials.
     *
     * @param string $provider
     * @param string $accessToken
     *
     * @return Authenticatable|null
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable
    {
        // Return the user that corresponds to provided credentials.
        // If the credentials are invalid, then return NULL.
        
        // $providerUser = Socialite::driver($provider)->userFromToken($accessToken);
    }
}
```

The next step is to bind `SocialUserResolverInterface` to your implementation.

You can do it by adding the appropriate key-value pair to `$bindings` property in `AppServiceProvider`:

```php
<?php

namespace App\Providers;

use App\Resolvers\SocialUserResolver;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        SocialUserResolverInterface::class => SocialUserResolver::class,
    ];
}
```

You are done!

## Usage

Example of usage with `axios`:

```javascript
axios.post('/oauth/token', {
    grant_type: 'social', // static 'social' value
    client_id: clientId, // client id
    client_secret: clientSecret, // client secret
    provider: providerName, // name of provider (e.g., 'facebook', 'google' etc.)
    access_token: providerAccessToken, // access token issued by specified provider
  })
  .then((response) => {
    const {
      access_token: accessToken,
      expires_in: expiresIn,
      refresh_token: refreshToken,
    } = response.data;

    // success logic
  })
  .catch((error) => {
    const {
      message,
      hint,
    } = error.response.data;

    // error logic
  });
```

Example of usage with `guzzle`:

```php
<?php

$http = new GuzzleHttp\Client();

$response = $http->post($domain . '/oauth/token', [
    RequestOptions::FORM_PARAMS => [
        'grant_type' => 'social', // static 'social' value
        'client_id' => $clientId, // client id
        'client_secret' => $clientSecret, // client secret
        'provider' => $providerName, // name of provider (e.g., 'facebook', 'google' etc.)
        'access_token' => $providerAccessToken, // access token issued by specified provider
    ],
    RequestOptions::HTTP_ERRORS => false,
]);
$data = json_decode($response->getBody()->getContents(), true);

if ($response->getStatusCode() === Response::HTTP_OK) {
    $accessToken = array_get($data, 'access_token');
    $expiresIn = array_get($data, 'expires_in');
    $refreshToken = array_get($data, 'refresh_token');

    // success logic
} else {
    $message = array_get($data, 'message');
    $hint = array_get($data, 'hint');

    // error logic
}
```

## Testing

You can run the tests with:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.