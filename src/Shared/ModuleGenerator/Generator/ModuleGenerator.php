<?php

namespace  BasePackage\Shared\ModuleGenerator\Generator;

use Illuminate\Support\Str;
use Nwidart\Modules\Generators\ModuleGenerator as BaseModuleGenerator;
use Nwidart\Modules\Support\Config\GenerateConfigReader;

/**
 * ModuleGenerator implementation for Qhub.
 *
 * This class overrides the base ModuleGenerator class from Laravel Modules package
 * in order to implement following improvements:
 *
 *  - it allows using replacements in the names of created files (see applyReplacementsToFilePath() method)
 *  - it adds new "CLEAN_MODULE_NAMESPACE" replacement which correctly deals with the base module's namespace
 *    containing slashes (like ours "Modules").
 *  - it overrides generateResources() method to get rid of the call to "module:route-provider" command.
 *    This allows us to _not_ generate the RouteServiceProvider.php file which we replaced with better
 *    solution.
 */
class ModuleGenerator extends BaseModuleGenerator
{
    public function __construct(
        $name,
        private $parentName = null,
    ) {
        parent::__construct($name);
    }

    private const PLACEHOLDERS = [
        '$LOWER_NAME$',
        '$STUDLY_NAME$',
        '$MODULE_NAMESPACE$',
        '$PROVIDER_NAMESPACE$',
    ];

    public function generate(): int
    {
        $name = $this->getName();

        if ($this->module->has($name)) {
            if ($this->force) {
                $this->module->delete($name);
            } else {
                $this->console->error("Module [{$name}] already exist!");

                return E_ERROR;
            }
        }

        $this->generateModuleJsonFile();
        $this->generateFolders();

        if ($this->type !== 'plain') {
            $this->generateFiles();
            $this->generateResources();
        }

        if ($this->type === 'plain') {
            $this->cleanModuleJsonFile();
        }

        $this->activator->setActiveByName($name, $this->isActive);

        $this->console->info("Module [{$name}] created successfully.");

        return 0;
    }

    private function generateModuleJsonFile()
    {
        if (!empty($this->parentName)) {
            $path = $this->module->getModulePath(Str::studly($this->parentName)) . $this->getName() . '/module.json';
        } else {
            $path = $this->module->getModulePath($this->getName()) . 'module.json';
        }
        if (!$this->filesystem->isDirectory($dir = dirname($path))) {
            $this->filesystem->makeDirectory($dir, 0775, true);
        }

        $this->filesystem->put($path, $this->getStubContents('json'));

        $this->console->info("Created : {$path}");
    }

    private function cleanModuleJsonFile()
    {
        $path = $this->module->getModulePath($this->getName()) . 'module.json';

        $content = $this->filesystem->get($path);
        $namespace = $this->getModuleNamespaceReplacement();
        $studlyName = $this->getStudlyNameReplacement();
        if (!empty($this->parentName)) {
            $provider = '"' . $namespace . '\\\\' . Str::studly($this->parentName) . '\\\\' . $studlyName
                . '\\\\Providers\\\\' . $studlyName . 'ServiceProvider"';
        } else {
            $provider = '"' . $namespace . '\\\\' . $studlyName . '\\\\Providers\\\\' . $studlyName . 'ServiceProvider"';
        }
        $content = str_replace($provider, '', $content);

        $this->filesystem->put($path, $content);
    }

    public function generateFiles()
    {
        foreach ($this->getFiles() as $stub => $file) {
            if (Str::contains($file, self::PLACEHOLDERS)) {
                $file = $this->applyReplacementsToFilePath($file);
            }

            $path = $this->module->getModulePath($this->getName()) . $file;

            if (!$this->filesystem->isDirectory($dir = dirname($path))) {
                $this->filesystem->makeDirectory($dir, 0775, true);
            }
            $this->filesystem->put($path, $this->getStubContents($stub));
            $this->console->info("Created : {$path}");
        }
    }

    public function generateResources()
    {
        if (GenerateConfigReader::read('seeder')->generate() === true) {
            $this->console->call('module:make-seed', [
                'name' => $this->getName(),
                'module' => $this->getName(),
                '--master' => true,
            ]);
        }

        if (GenerateConfigReader::read('provider')->generate() === true) {
            $this->console->call('module:make-provider', [
                'name' => $this->getName() . 'ServiceProvider',
                'module' => $this->getName(),
                '--master' => true,
            ]);
        }

        if (GenerateConfigReader::read('controller')->generate() === true) {
            $options = $this->type == 'api' ? ['--api' => true] : [];
            $this->console->call('module:make-controller', [
                'controller' => $this->getName() . 'Controller',
                'module' => $this->getName(),
            ] + $options);
        }
    }

    protected function getCleanModuleNamespaceReplacement()
    {
        $namespace = $this->module->config('namespace');
        if (!empty($this->parentName)) {
            return $namespace . '\\' . Str::studly($this->parentName);
        }

        $path = $this->module->getPath() . '/' . $this->getName() . "/module.json";
        if (file_exists($path)) {
            $moduleJson = json_decode(
                file_get_contents($path),
                true
            );
            $namespace = $moduleJson['namespace'] ?? $this->module->config('namespace');
            config(['modules.namespace' => $namespace]);
        }

        return $namespace;
    }

    protected function getSnakeModuleNameReplacement(): string
    {
        return Str::snake($this->getName());
    }

    protected function getCamelModuleNameReplacement(): string
    {
        return Str::camel($this->getName());
    }

    protected function getSnakePluralModuleNameReplacement(): string
    {
        return Str::snake(Str::pluralStudly($this->getName()));
    }

    protected function getModuleNamespaceReplacement()
    {
        $namespace = $this->module->config('namespace');
        if (!empty($this->parentName)) {
            $namespace = $namespace . '\\' . Str::studly($this->parentName);
        } else {
            $path = $this->module->getPath() . '/' . $this->getName() . "/module.json";
            if (file_exists($path)) {
                $moduleJson = json_decode(
                    file_get_contents($path),
                    true
                );
                $namespace = $moduleJson['namespace'] ?? $this->module->config('namespace');
                config(['modules.namespace' => $namespace]);
            }
        }
        return str_replace('\\', '\\\\', $namespace);
    }


    private function applyReplacementsToFilePath(string $path): string
    {
        return Str::replace(
            self::PLACEHOLDERS,
            [
                $this->getLowerNameReplacement(),
                $this->getStudlyNameReplacement(),
                $this->getModuleNamespaceReplacement(),
                $this->getProviderNamespaceReplacement(),
            ],
            $path
        );
    }
}
