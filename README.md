# Laravel Passport Social Grant

[![Packagist](https://badgen.net/packagist/v/coderello/laravel-passport-social-grant)](https://packagist.org/packages/coderello/laravel-passport-social-grant)
[![GitHub tag](https://badgen.net/github/tag/coderello/laravel-passport-social-grant)](https://github.com/coderello/laravel-passport-social-grant/releases)
[![License](https://badgen.net/packagist/license/coderello/laravel-passport-social-grant)](LICENSE.md)
[![Downloads](https://badgen.net/packagist/dt/coderello/laravel-passport-social-grant)](https://packagist.org/packages/coderello/laravel-passport-social-grant/stats)
[![tests](https://github.com/coderello/laravel-passport-social-grant/workflows/tests/badge.svg)](https://github.com/coderello/laravel-passport-social-grant/actions)

This package adds a social grant for your OAuth2 server. It can be useful if have an API and want to provide the ability for your users to login/register through social networks.

As a result you will be able to exchange `access_token`, issued by the OAuth2 server of any social provider, to `access_token` and `refresh_token` issued by your own OAuth2 server. 
You will receive this `access_token` and return the user instance that corresponds to it on your own.

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

use GuzzleHttp\Client;
use Illuminate\Support\Arr;

$http = new Client;

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
    $accessToken = Arr::get($data, 'access_token');
    $expiresIn = Arr::get($data, 'expires_in');
    $refreshToken = Arr::get($data, 'refresh_token');

    // success logic
} else {
    $message = Arr::get($data, 'message');
    $hint = Arr::get($data, 'hint');

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

## Credits

Created by [Illia Sakovich](https://github.com/hivokas)

Maintained by [Ankur Kumar](https://github.com/ankurk91)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
