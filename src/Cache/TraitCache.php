<?php

namespace App\ESolutions\Cache;

use Illuminate\Support\Facades\Cache;

trait TraitCache
{
    static function getNameCache(): string
    {
        $model = self::getModel();
        $tableName = $model->getTable();

        return HelperCache::nameCache($tableName);
    }

    static function clearCache(): void
    {
        $cacheName = self::getNameCache();
        Cache::forget($cacheName);
    }


}
