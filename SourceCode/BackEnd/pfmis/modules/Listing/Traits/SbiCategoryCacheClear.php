<?php

namespace Module\Listing\Traits;

use Illuminate\Support\Facades\Cache;

trait SbiCategoryCacheClear{
    protected static function boot()
    {
        parent::boot();

        /**
         * After model is created, or whatever action, clear cache.
         */
        static::saved(function () {
            Cache::forget('sbi_categorys');
        });

        static::updated(function () {
            Cache::forget('sbi_categorys');
        });

        static::deleted(function () {
            Cache::forget('sbi_categorys');
        });
    }
}
