<?php

namespace Hivokas\LaravelPassportSocialGrant\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Hivokas\LaravelPassportSocialGrant\Providers\SocialGrantServiceProvider;

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
