<?php

namespace Module\Listing\Traits;

use Illuminate\Support\Facades\Cache;

trait ExpenseCacheClear{
    protected static function boot()
    {
        parent::boot();

        /**
         * After model is created, or whatever action, clear cache.
         */
        static::saved(function () {
            Cache::forget('expenses');
        });

        static::updated(function () {
            Cache::forget('expenses');
        });

        static::deleted(function () {
            Cache::forget('expenses');
        });
    }
}
