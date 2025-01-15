<?php

declare(strict_types=1);

namespace  BasePackage\Shared\ModuleGenerator\Console\Command;

use Nwidart\Modules\Commands\ModelMakeCommand as BaseModelMakeCommand;
use  BasePackage\Shared\ModuleGenerator\Traits\HandleNamespace;

class ModelMakeCommand extends BaseModelMakeCommand
{
    use HandleNamespace;

    protected function getTemplateContents()
    {
        $module = $this->updateNamespace($this->laravel['modules'], $this->getModuleName());
        return parent::getTemplateContents();
    }
}
