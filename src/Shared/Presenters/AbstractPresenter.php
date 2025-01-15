<?php

declare(strict_types=1);

namespace  BasePackage\Shared\Presenters;

abstract class AbstractPresenter
{
    public static function collection(iterable $collection, ...$additionalParams): array
    {
        $result = [];
        foreach ($collection as $item) {
            $data = (new static($item, ...$additionalParams))->getData(isListing: true);

            if ($data === null) {
                continue;
            }

            $result[] = $data;
        }

        return $result;
    }

    public function getData(bool $isListing = false): ?array
    {
        $array =  $this->present($isListing);
        foreach ($array as $key => $value){
            if($value === 'delete_this_row'){
                unset($array[$key]);
            }
            if($value == 'delete_this_array'){
                return null;
            }
        }
        return $array;
    }

    abstract protected function present(bool $isListing = false): ?array;

    public function generalCondition($condition,$callable){

        if(!$condition) {
            return 'delete_this_array';
        }

        if (is_callable($callable)) {
            return $callable();
        }

        return $callable;
    }

    public function when($condition, $callable, $deleteRow = true)
    {
        if (!$condition) {
            return 'delete_this_row';
        } elseif ($deleteRow) {
            if (is_callable($callable)) {
                return $callable();
            }

            return $callable;
        }

        return null;
    }
}
