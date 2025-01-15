<?php

declare(strict_types=1);

namespace  BasePackage\Shared\Facade;

use Illuminate\Support\Facades\Facade;

class Json extends Facade
{
    public const SUCCESS_WITH_SINGLE_PAYLOAD_OBJECT = 'SUCCESS_WITH_SINGLE_PAYLOAD_OBJECT';
    public const SUCCESS_WITH_LIST_PAYLOAD_OBJECTS = 'SUCCESS_WITH_LIST_PAYLOAD_OBJECTS';
    public const SUCCESS_WITHOUT_PAYLOAD = 'SUCCESS_WITHOUT_PAYLOAD';

    protected static function getFacadeAccessor()
    {
        return 'json';
    }
}
