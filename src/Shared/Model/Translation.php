<?php

declare(strict_types=1);

namespace  BasePackage\Shared\Model;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = ['locale', 'field', 'content'];

    public function translatable()
    {
        return $this->morphTo();
    }
}
