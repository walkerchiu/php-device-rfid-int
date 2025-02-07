<?php

namespace WalkerChiu\DeviceRFID;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\DeviceRFID\Models\Entities\Reader;
use WalkerChiu\DeviceRFID\Models\Entities\Card;
use WalkerChiu\DeviceRFID\Models\Entities\CardLang;

class CardLangTest extends \Orchestra\Testbench\TestCase
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
     * A basic functional test on CardLang.
     *
     * For WalkerChiu\Core\Models\Entities\Lang
     *     WalkerChiu\DeviceRFID\Models\Entities\CardLang
     *
     * @return void
     */
    public function testCardLang()
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
        factory(Card::class, 2)->create();
        factory(CardLang::class)->create(['morph_id' => 1, 'morph_type' => Card::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(CardLang::class)->create(['morph_id' => 1, 'morph_type' => Card::class, 'code' => 'en_us', 'key' => 'description']);
        factory(CardLang::class)->create(['morph_id' => 1, 'morph_type' => Card::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(CardLang::class)->create(['morph_id' => 1, 'morph_type' => Card::class, 'code' => 'en_us', 'key' => 'name']);
        factory(CardLang::class)->create(['morph_id' => 2, 'morph_type' => Card::class, 'code' => 'en_us', 'key' => 'name']);
        factory(CardLang::class)->create(['morph_id' => 2, 'morph_type' => Card::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get records after creation
            // When
            $records = CardLang::all();
            // Then
            $this->assertCount(6, $records);

        // Get record's morph
            // When
            $record = CardLang::find(1);
            // Then
            $this->assertNotNull($record);
            $this->assertInstanceOf(Card::class, $record->morph);

        // Scope query on whereCode
            // When
            $records = CardLang::ofCode('en_us')
                               ->get();
            // Then
            $this->assertCount(4, $records);

        // Scope query on whereKey
            // When
            $records = CardLang::ofKey('name')
                               ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereCodeAndKey
            // When
            $records = CardLang::ofCodeAndKey('en_us', 'name')
                               ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereMatch
            // When
            $records = CardLang::ofMatch('en_us', 'name', 'Hello')
                               ->get();
            // Then
            $this->assertCount(1, $records);
            $this->assertTrue($records->contains('id', 1));
    }

    /**
     * A basic functional test on CardLang.
     *
     * For WalkerChiu\Core\Models\Entities\LangTrait
     *     WalkerChiu\DeviceRFID\Models\Entities\CardLang
     *
     * @return void
     */
    public function testCard()
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
        factory(Card::class, 2)->create();
        factory(CardLang::class)->create(['morph_id' => 1, 'morph_type' => Card::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(CardLang::class)->create(['morph_id' => 1, 'morph_type' => Card::class, 'code' => 'en_us', 'key' => 'description']);
        factory(CardLang::class)->create(['morph_id' => 1, 'morph_type' => Card::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(CardLang::class)->create(['morph_id' => 1, 'morph_type' => Card::class, 'code' => 'en_us', 'key' => 'name']);
        factory(CardLang::class)->create(['morph_id' => 2, 'morph_type' => Card::class, 'code' => 'en_us', 'key' => 'name']);
        factory(CardLang::class)->create(['morph_id' => 2, 'morph_type' => Card::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get lang of record
            // When
            $record_1 = Card::find(1);
            $lang_1   = CardLang::find(1);
            $lang_4   = CardLang::find(4);
            // Then
            $this->assertNotNull($record_1);
            $this->assertTrue(!$lang_1->is_current);
            $this->assertTrue($lang_4->is_current);
            $this->assertCount(4, $record_1->langs);
            $this->assertInstanceOf(CardLang::class, $record_1->findLang('en_us', 'name', 'entire'));
            $this->assertEquals(4, $record_1->findLang('en_us', 'name', 'entire')->id);
            $this->assertEquals(4, $record_1->findLangByKey('name', 'entire')->id);
            $this->assertEquals(2, $record_1->findLangByKey('description', 'entire')->id);

        // Get lang's histories of record
            // When
            $histories_1 = $record_1->getHistories('en_us', 'name');
            $record_2 = Card::find(2);
            $histories_2 = $record_2->getHistories('en_us', 'name');
            // Then
            $this->assertCount(1, $histories_1);
            $this->assertCount(0, $histories_2);
    }
}
