<?php

namespace WalkerChiu\DeviceRFID\Models\Entities;

use WalkerChiu\Core\Models\Entities\Entity;
use WalkerChiu\Core\Models\Entities\LangTrait;

class ReaderState extends Entity
{
    use LangTrait;



    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.device-rfid.readers_states');

        $this->fillable = array_merge($this->fillable, [
            'reader_id',
            'serial',
            'identifier', 'mean',
        ]);

        parent::__construct($attributes);
    }

    /**
     * Get it's lang entity.
     *
     * @return Lang
     */
    public function lang()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-device-rfid.onoff.core-lang_core')
        ) {
            return config('wk-core.class.core.langCore');
        } else {
            return config('wk-core.class.device-rfid.readerStateLang');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-device-rfid.onoff.core-lang_core')
        ) {
            return $this->langsCore();
        } else {
            return $this->hasMany(config('wk-core.class.device-rfid.readerStateLang'), 'morph_id', 'id');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reader()
    {
        return $this->belongsTo(config('wk-core.class.device-rfid.reader'), 'reader_id', 'id');
    }
}
