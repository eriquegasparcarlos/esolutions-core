<?php

namespace App\ESolutions\Cache;

use Modules\Company\Models\Company;

class HelperCache
{
    static function getNameCache($table): string
    {
        $company = Company::query()->first();
        $externalId = $company ? $company->external_id : 'default';

        return "cache_{$externalId}_{$table}";
    }

    static function nameCache($name, $type = 'table'): string
    {
        $fqdn = 'k';

        return "{$fqdn}_{$type}_{$name}";
    }
}
