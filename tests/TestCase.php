<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use AiEditor\AiTextEditor\AiTextEditorServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            AiTextEditorServiceProvider::class,
        ];
    }
}
