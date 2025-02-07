<?php

namespace WalkerChiu\DeviceRFID;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\DeviceRFID\Models\Entities\Reader;
use WalkerChiu\DeviceRFID\Models\Entities\ReaderState;
use WalkerChiu\DeviceRFID\Models\Entities\ReaderStateLang;

class ReaderStateLangTest extends \Orchestra\Testbench\TestCase
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
     * A basic functional test on ReaderStateLang.
     *
     * For WalkerChiu\Core\Models\Entities\Lang
     *     WalkerChiu\DeviceRFID\Models\Entities\ReaderStateLang
     *
     * @return void
     */
    public function testReaderStateLang()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-device-rfid.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-device-rfid.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-device-rfid.soft_delete', 1);

        // Give
        factory(Reader::class)->create();
        factory(ReaderState::class, 2)->create();
        factory(ReaderStateLang::class)->create(['morph_id' => 1, 'morph_type' => ReaderState::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(ReaderStateLang::class)->create(['morph_id' => 1, 'morph_type' => ReaderState::class, 'code' => 'en_us', 'key' => 'description']);
        factory(ReaderStateLang::class)->create(['morph_id' => 1, 'morph_type' => ReaderState::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(ReaderStateLang::class)->create(['morph_id' => 1, 'morph_type' => ReaderState::class, 'code' => 'en_us', 'key' => 'name']);
        factory(ReaderStateLang::class)->create(['morph_id' => 2, 'morph_type' => ReaderState::class, 'code' => 'en_us', 'key' => 'name']);
        factory(ReaderStateLang::class)->create(['morph_id' => 2, 'morph_type' => ReaderState::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get records after creation
            // When
            $records = ReaderStateLang::all();
            // Then
            $this->assertCount(6, $records);

        // Get record's morph
            // When
            $record = ReaderStateLang::find(1);
            // Then
            $this->assertNotNull($record);
            $this->assertInstanceOf(ReaderState::class, $record->morph);

        // Scope query on whereCode
            // When
            $records = ReaderStateLang::ofCode('en_us')
                                      ->get();
            // Then
            $this->assertCount(4, $records);

        // Scope query on whereKey
            // When
            $records = ReaderStateLang::ofKey('name')
                                      ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereCodeAndKey
            // When
            $records = ReaderStateLang::ofCodeAndKey('en_us', 'name')
                                      ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereMatch
            // When
            $records = ReaderStateLang::ofMatch('en_us', 'name', 'Hello')
                                      ->get();
            // Then
            $this->assertCount(1, $records);
            $this->assertTrue($records->contains('id', 1));
    }

    /**
     * A basic functional test on ReaderStateLang.
     *
     * For WalkerChiu\Core\Models\Entities\LangTrait
     *     WalkerChiu\DeviceRFID\Models\Entities\ReaderStateLang
     *
     * @return void
     */
    public function testReaderState()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-device-rfid.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-device-rfid.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-device-rfid.soft_delete', 1);

        // Give
        factory(Reader::class)->create();
        factory(ReaderState::class, 2)->create();
        factory(ReaderStateLang::class)->create(['morph_id' => 1, 'morph_type' => ReaderState::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(ReaderStateLang::class)->create(['morph_id' => 1, 'morph_type' => ReaderState::class, 'code' => 'en_us', 'key' => 'description']);
        factory(ReaderStateLang::class)->create(['morph_id' => 1, 'morph_type' => ReaderState::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(ReaderStateLang::class)->create(['morph_id' => 1, 'morph_type' => ReaderState::class, 'code' => 'en_us', 'key' => 'name']);
        factory(ReaderStateLang::class)->create(['morph_id' => 2, 'morph_type' => ReaderState::class, 'code' => 'en_us', 'key' => 'name']);
        factory(ReaderStateLang::class)->create(['morph_id' => 2, 'morph_type' => ReaderState::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get lang of record
            // When
            $record_1 = ReaderState::find(1);
            $lang_1   = ReaderStateLang::find(1);
            $lang_4   = ReaderStateLang::find(4);
            // Then
            $this->assertNotNull($record_1);
            $this->assertTrue(!$lang_1->is_current);
            $this->assertTrue($lang_4->is_current);
            $this->assertCount(4, $record_1->langs);
            $this->assertInstanceOf(ReaderStateLang::class, $record_1->findLang('en_us', 'name', 'entire'));
            $this->assertEquals(4, $record_1->findLang('en_us', 'name', 'entire')->id);
            $this->assertEquals(4, $record_1->findLangByKey('name', 'entire')->id);
            $this->assertEquals(2, $record_1->findLangByKey('description', 'entire')->id);

        // Get lang's histories of record
            // When
            $histories_1 = $record_1->getHistories('en_us', 'name');
            $record_2 = ReaderState::find(2);
            $histories_2 = $record_2->getHistories('en_us', 'name');
            // Then
            $this->assertCount(1, $histories_1);
            $this->assertCount(0, $histories_2);
    }
}
