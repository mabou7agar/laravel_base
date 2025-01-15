<?php

declare(strict_types=1);

namespace  BasePackage\Shared\Module;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

abstract class ModuleServiceProvider extends ServiceProvider
{
    abstract public static function getModuleName(): string;

    public static function getModuleNameLower(): string
    {
        return strtolower(string: static::getModuleName());
    }

    public function mapRoutes(): void
    {
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            $this->getModulePath('Resources/config/config.php'),
            static::getModuleNameLower(),
        );
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom($this->getModulePath('Database/Migrations'));
        $tenantPaths = (array) config('tenancy.db.tenant-migrations-path');
        $tenantPaths[] = $this->getModulePath('Database/Migrations/Tenant');
        config(['tenancy.db.tenant-migrations-path' => $tenantPaths]);
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(
            $this->getModulePath('Resources/lang'),
            static::getModuleNameLower()
        );
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(
            $this->getModulePath('Resources/views'),
            static::getModuleNameLower()
        );
    }

    protected function registerRoutes(): void
    {
        $this->booted(function () {
            if ($this->app->routesAreCached()) {
                $this->loadCachedRoutes();
            } else {
                $this->loadRoutes();

                $this->app->booted(function () {
                    $this->app['router']->getRoutes()->refreshNameLookups();
                    $this->app['router']->getRoutes()->refreshActionLookups();
                });
            }
        });
    }

    protected function resolveBaseModulePath(): string
    {
        $fqcn = static::class;

        // Drop the last two segments to get the module path, not the file's FQCN
        // or the provider's namespace.
        $path = implode('/', explode('\\', $fqcn, -2));

        // Combine our resolved path with an absolute path of our application

        if (str_contains($path, 'Modules/')) {
            $absoluteAppPath = base_path('modules');
            $path = Str::after($path, 'Modules');
        } elseif (str_contains(__DIR__, 'src')) {
            $absoluteAppPath = Str::before(__DIR__, 'src');
            $absoluteAppPath = Str::beforeLast($absoluteAppPath,'/').'/';
            $path = '/src';
        } else {
            $absoluteAppPath = Str::before(__DIR__, $path);
        }

        return $absoluteAppPath . $path;
    }

    private function loadCachedRoutes(): void
    {
        $this->app->booted(function () {
            require $this->app->getCachedRoutesPath();
        });
    }

    private function loadRoutes(): void
    {
        $this->app->call([$this, 'mapRoutes']);
    }

    protected function getModulePath($path = '')
    {
        $modulePath = $this->resolveBaseModulePath();
        return $modulePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}
