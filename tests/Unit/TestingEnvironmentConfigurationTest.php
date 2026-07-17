<?php

namespace Tests\Unit;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use PHPUnit\Framework\TestCase;

class TestingEnvironmentConfigurationTest extends TestCase
{
    public function test_phpunit_boots_with_uncached_test_database_configuration(): void
    {
        $app = require __DIR__.'/../../bootstrap/app.php';

        $this->assertInstanceOf(Application::class, $app);

        try {
            $app->make(Kernel::class)->bootstrap();

            $this->assertTrue($app->environment('testing'));
            $this->assertFalse($app->configurationIsCached());
            $this->assertSame('pgsql', $app->make('config')->get('database.default'));
            $this->assertSame('bang_testing', $app->make('config')->get('database.connections.pgsql.database'));
        } finally {
            HandleExceptions::flushState($this);
            $app->flush();
        }
    }
}
