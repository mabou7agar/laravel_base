<?php

namespace  BasePackage\Shared\Traits;

trait PaginationInfo
{
    protected function getPaginationInformation(int $page, int $pageSize, int $itemsTotalCount): array
    {
        $lastPage = ceil($itemsTotalCount / $pageSize);
        return [
            'pagination' => [
                'page' => $page,
                'page_size' => $pageSize,
                'next_page' => $lastPage > $page ? $page + 1 : $page,
                'last_page' => $lastPage > $page ? $lastPage : $page,
                'result_count' => $itemsTotalCount,
            ],
        ];
    }
}
