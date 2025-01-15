<?php

declare(strict_types=1);

namespace  BasePackage\Shared\Filters;

use EloquentFilter\ModelFilter;

class SearchModelFilter extends ModelFilter
{
    public function id($id): SearchModelFilter
    {
        return $this->where('id', $id);
    }

    public function searchTranslation($field, $searchText): SearchModelFilter
    {
        return $this->whereHas('translations', function ($q) use ($field, $searchText) {
            return $q->where('field', $field)->whereLike('content', '%' . $searchText . '%');
        });
    }
}
