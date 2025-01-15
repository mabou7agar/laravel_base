<?php

namespace  BasePackage\Shared\ModuleGenerator\Console\Command;

use Nwidart\Modules\Commands\ModuleMakeCommand as BaseModuleMakeCommand;
use Nwidart\Modules\Contracts\ActivatorInterface;
use Symfony\Component\Console\Input\InputArgument;
use  BasePackage\Shared\ModuleGenerator\Generator\ModuleGenerator;

/**
 * The override of base "module:make" command.
 *
 * This overrides the base command in order to provide our own ModuleGenerator implementation.
 */
class ModuleMakeCommand extends BaseModuleMakeCommand
{
    public function handle(): int
    {
        $name = $this->argument('name');
        $parentName = $this->argument('parentName');

        $success = true;

        $code = with(new ModuleGenerator($name, $parentName))
            ->setFilesystem($this->laravel['files'])
            ->setModule($this->laravel['modules'])
            ->setConfig($this->laravel['config'])
            ->setActivator($this->laravel[ActivatorInterface::class])
            ->setConsole($this)
            ->setForce($this->option('force'))
            ->setType($this->getModuleType())
            ->setActive(!$this->option('disabled'))
            ->generate();

        if ($code === E_ERROR) {
            $success = false;
        }

        return $success ? 0 : E_ERROR;
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The names of modules to create.'],
            ['parentName', InputArgument::OPTIONAL, 'The name of parent module to put new module in.'],
        ];
    }

    /**
     * Get module type.
     *
     * @return string
     */
    private function getModuleType(): string
    {
        $isPlain = $this->option('plain');
        $isApi = $this->option('api');

        if ($isPlain && $isApi) {
            return 'web';
        }
        if ($isPlain) {
            return 'plain';
        } elseif ($isApi) {
            return 'api';
        } else {
            return 'web';
        }
    }
}
