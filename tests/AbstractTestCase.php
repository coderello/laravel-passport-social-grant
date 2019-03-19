<?php

namespace Coderello\LaravelPassportSocialGrant\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Coderello\LaravelPassportSocialGrant\Providers\SocialGrantServiceProvider;

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
