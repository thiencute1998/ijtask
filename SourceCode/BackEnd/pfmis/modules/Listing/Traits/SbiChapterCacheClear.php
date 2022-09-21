<?php

namespace Module\Listing\Traits;

use Illuminate\Support\Facades\Cache;

trait SbiChapterCacheClear{
    protected static function boot()
    {
        parent::boot();

        /**
         * After model is created, or whatever action, clear cache.
         */
        static::saved(function () {
            Cache::forget('sbi_chapters');
        });

        static::updated(function () {
            Cache::forget('sbi_chapters');
        });

        static::deleted(function () {
            Cache::forget('sbi_chapters');
        });
    }
}
