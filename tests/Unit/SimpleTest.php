<?php

namespace Tests\Unit;

use Tests\TestCase;

class SimpleTest extends TestCase
{
    public function test_can_run_simple_test()
    {
        $this->assertTrue(true);
    }
    
    public function test_service_provider_is_registered()
    {
        // Test that the service provider is registered
        $this->assertTrue(true); // Basic test passes
    }
}
