<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use LaravelStarterKit\MultiStack\MultiStackServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Register the service provider
        $this->app->register(MultiStackServiceProvider::class);
        
        // Run migrations
        $this->artisan('migrate');
    }

    protected function tearDown(): void
    {
        // Clean up after tests
        $this->artisan('migrate:rollback');
        
        parent::tearDown();
    }
}
