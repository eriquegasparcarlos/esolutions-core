<?php

namespace App\ESolutions\Utils;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CacheTable
{
    public static function getTables(): array
    {
        $helpersPath = app_path('Cache');
        $files = File::allFiles($helpersPath);

        $cacheData = [];

        foreach ($files as $file) {
            $relativePath = $file->getRelativePath();
            $depth = substr_count($relativePath, DIRECTORY_SEPARATOR);
            if ($depth > 1) {
                continue;
            }
            $namespace = 'App\\Cache';
            if (!empty($relativePath)) {
                $subNamespace = str_replace('/', '\\', $relativePath);
                $namespace .= '\\' . $subNamespace;
            }
            $className = $namespace . '\\' . $file->getFilenameWithoutExtension();
            if (class_exists($className) && method_exists($className, 'getCache')) {
                // Quita el sufijo 'Cache'
                $baseName = class_basename($className);
                $name = preg_replace('/Cache$/', '', $baseName);

                // Pluraliza y pone en minúscula
                $key = Str::plural(lcfirst($name));
                $cacheData[$key] = $className::getCache();
            }
        }

        return $cacheData;
    }
}
