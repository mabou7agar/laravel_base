<?php

declare(strict_types=1);

namespace  BasePackage\Shared\ModuleGenerator\Traits;

use Nwidart\Modules\Laravel\LaravelFileRepository;
use Nwidart\Modules\Module;

trait HandleNamespace
{
    public function updateNamespace(LaravelFileRepository $modules, string $moduleName): Module
    {
        $module = $modules->findOrFail($moduleName);
        $moduleJson = json_decode(file_get_contents($module->getPath() . "/module.json"), true);
        $namespace = $moduleJson['namespace'] ?? $modules->config('namespace');
        config(['modules.namespace' => $namespace]);

        return $module;
    }
}
