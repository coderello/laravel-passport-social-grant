<?php

namespace Hivokas\LaravelPassportSocialGrant\Tests;

use Hivokas\LaravelPassportSocialGrant\Providers\SocialGrantServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class AbstractTestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SocialGrantServiceProvider::class,
        ];
    }
}
