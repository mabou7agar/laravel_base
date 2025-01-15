<?php

namespace  BasePackage\Shared\ModuleGenerator\Console\Command;

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\ControllerMakeCommand as BaseControllerMakeCommand;
use Nwidart\Modules\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;
use  BasePackage\Shared\ModuleGenerator\Traits\HandleNamespace;

/**
 * The override of base "module:make-controller" command.
 *
 * This overrides the base command in order to provide more replacements to
 * the controller's stub file.
 */
class ControllerMakeCommand extends BaseControllerMakeCommand
{
    use HandleNamespace;

    protected function getTemplateContents(): string
    {
        //Update Module Namespace in runtime
        $module = $this->updateNamespace($this->laravel['modules'], $this->getModuleName());
        $controllerType = str_replace(
            dirname($this->getDestinationFilePath(), 2) . '/',
            '',
            dirname($this->getDestinationFilePath())
        );

        return (new Stub($this->getStubName(), [
            'MODULENAME'        => $module->getStudlyName(),
            'CONTROLLERNAME'    => $this->getControllerName(),
            'NAMESPACE'         => $module->getStudlyName(),
            'CLASS_NAMESPACE'   => $this->getClassNamespace($module),
            'CLASS'             => $this->getControllerNameWithoutNamespace(),
            'LOWER_NAME'        => $module->getLowerName(),
            'MODULE'            => $this->getModuleName(),
            'NAME'              => $this->getModuleName(),
            'STUDLY_NAME'       => $module->getStudlyName(),
            'CAMEL_MODULE_NAME' => Str::camel($module->getStudlyName()),
            'MODULE_NAMESPACE'  => $this->laravel['modules']->config('namespace'),
            'CONTROLLER_TYPE'   => $controllerType,

            // Our custom replacements
            'SNAKE_MODULE_NAME' => Str::snake($module->getStudlyName()),
            'SNAKE_PLURAL_MODULE_NAME' => Str::snake(Str::pluralStudly($module->getStudlyName())),
        ]))->render();
    }

    private function getControllerNameWithoutNamespace(): string
    {
        return class_basename($this->getControllerName());
    }

    protected function getArguments()
    {
        return [
            ['controller', InputArgument::REQUIRED, 'The name of the controller class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module to use.'],
            ['parentName', InputArgument::OPTIONAL, 'The name of parent module to use.'],
        ];
    }
}
