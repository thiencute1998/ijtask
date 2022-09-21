<?php

namespace Module\Listing\Traits;

use Illuminate\Support\Facades\Cache;

trait SbiItemCacheClear{
    protected static function boot()
    {
        parent::boot();

        /**
         * After model is created, or whatever action, clear cache.
         */
        static::saved(function () {
            Cache::forget('sbi_items');
        });

        static::updated(function () {
            Cache::forget('sbi_items');
        });

        static::deleted(function () {
            Cache::forget('sbi_items');
        });
    }
}
