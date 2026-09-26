# Upgrade Guide

## Upgrading from 4.x to 5.0

### Resolver receives the client

`SocialUserResolverInterface::resolveUserByProviderCredentials()` now receives the client that requested the token
as a third argument. This lets you allow different social providers per client, or pick a user provider per client.

Update the method signature in your resolver:

```diff
+use League\OAuth2\Server\Entities\ClientEntityInterface;

 class SocialUserResolver implements SocialUserResolverInterface
 {
-    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable
+    public function resolveUserByProviderCredentials(string $provider, string $accessToken, ClientEntityInterface $client): ?Authenticatable
     {
         // ...
     }
 }
```

If you don't need the client, you can ignore the argument, but the signature must still accept it.

If you extend `Coderello\SocialGrant\Grants\SocialGrant` and override `validateUser()`,
it now receives the client as well:

```php
public function validateUser(ServerRequestInterface $request, ClientEntityInterface $client): UserEntity
```

### Allow the `social` grant on your clients

Passport v13 checks the `grant_types` column of the `oauth_clients` table,
and rejects any grant that is not listed there with an `unauthorized_client` error.
Add `social` to every client that should use this grant:

```php
use Laravel\Passport\Client;

$client = Client::findOrFail($clientId);
$client->grant_types = array_unique([...$client->grant_types, 'social']);
$client->save();
```

To enable it on many clients at once, you can run the same code in a migration.

If your `oauth_clients` table was created before Passport v13, it may not have a `grant_types` column.
Add it first using the
[migration described in the Passport upgrade guide](https://github.com/laravel/passport/blob/13.x/UPGRADE.md#oauth-client-table-changes-optional).
