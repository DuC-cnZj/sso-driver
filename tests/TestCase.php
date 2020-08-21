<?php

namespace DucCnzj\Tests;

use DucCnzj\Sso\SsoServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [SsoServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('sso.base_url', 'http://example.com');
    }
}