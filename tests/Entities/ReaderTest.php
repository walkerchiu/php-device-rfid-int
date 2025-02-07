<?php

namespace WalkerChiu\DeviceRFID;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\DeviceRFID\Models\Entities\Reader;
use WalkerChiu\DeviceRFID\Models\Entities\ReaderLang;

class ReaderTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\DeviceRFID\DeviceRFIDServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * A basic functional test on Reader.
     *
     * For WalkerChiu\DeviceRFID\Models\Entities\Reader
     * 
     * @return void
     */
    public function testReader()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-device-rfid.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-device-rfid.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-device-rfid.soft_delete', 1);

        // Give
        $record_1 = factory(Reader::class)->create();
        $record_2 = factory(Reader::class)->create();
        $record_3 = factory(Reader::class)->create(['is_enabled' => 1]);

        // Get records after creation
            // When
            $records = Reader::all();
            // Then
            $this->assertCount(3, $records);

        // Delete someone
            // When
            $record_2->delete();
            $records = Reader::all();
            // Then
            $this->assertCount(2, $records);

        // Resotre someone
            // When
            Reader::withTrashed()
                  ->find(2)
                  ->restore();
            $record_2 = Reader::find(2);
            $records = Reader::all();
            // Then
            $this->assertNotNull($record_2);
            $this->assertCount(3, $records);

        // Return Lang class
            // When
            $class = $record_2->lang();
            // Then
            $this->assertEquals($class, ReaderLang::class);

        // Scope query on enabled records
            // When
            $records = Reader::ofEnabled()
                             ->get();
            // Then
            $this->assertCount(1, $records);

        // Scope query on disabled records
            // When
            $records = Reader::ofDisabled()
                             ->get();
            // Then
            $this->assertCount(2, $records);
    }
}
