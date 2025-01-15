<?php

declare(strict_types=1);

namespace  BasePackage\Shared\Traits;

use EloquentFilter\Filterable;

use function class_basename;
use function config;

trait BaseFilterable
{
    use Filterable;

    public function provideFilter($filter = null)
    {
        if ($filter === null) {
            $classPath = get_class($this);
            $modelPos = strpos($classPath, 'Models');
            if ($modelPos === false) {
                $filter = config('eloquentfilter.namespace', 'App\\ModelFilters\\') . class_basename($this) . 'Filter';
            } else {
                $filter = substr($classPath, 0, $modelPos) . 'Filters\\' . class_basename($this) . 'Filter';
            }
        }

        return $filter;
    }
}
