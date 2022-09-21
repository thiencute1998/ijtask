<?php

namespace Module\Listing\Traits;

use Illuminate\Support\Facades\Cache;

trait RevenueCacheClear{
    protected static function boot()
    {
        parent::boot();

        /**
         * After model is created, or whatever action, clear cache.
         */
        static::saved(function () {
            Cache::forget('revenues');
        });

        static::updated(function () {
            Cache::forget('revenues');
        });

        static::deleted(function () {
            Cache::forget('revenues');
        });
    }
}
