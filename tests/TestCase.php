<?php

namespace PauloHortelan\RequestsGraphPulse\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected $enablesPackageDiscoveries = true;

    use RefreshDatabase;
    use WithWorkbench;

    // protected function getPackageProviders($app)
    // {
    //     return [
    //         \PauloHortelan\RequestsGraphPulse\RequestsServiceProvider::class,
    //         \Workbench\App\Providers\WorkbenchServiceProvider::class
    //     ];
    // }

    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     $this->artisan('migrate', [
    //         '--database' => 'testbench',
    //         '--realpath' => realpath(__DIR__ . '/../workbench/database/migrations'),
    //     ]);
    // }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
