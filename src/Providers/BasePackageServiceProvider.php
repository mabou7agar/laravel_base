<?php

declare(strict_types=1);

namespace BasePackage\Providers;

use BasePackage\Shared\Presenters\Json;
use Illuminate\Support\Facades\Route;
use BasePackage\Shared\Module\ModuleServiceProvider;

class BasePackageServiceProvider extends ModuleServiceProvider
{
    public static function getModuleName(): string
    {
        return 'BasePackage';
    }

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerMigrations();
        $this->handlePublish();
        $this->registerMigrations();
    }

    public function register(): void
    {
        $this->loadModulesFolder();
        $this->registerRoutes();
        $this->bindFacade();
        //$this->commands([]);
    }

    public function mapRoutes(): void
    {
        Route::prefix('api/base-package')
            ->middleware('api')
            ->group($this->getModulePath() . '/Resources/routes/api.php');
    }

    public function handlePublish()
    {
        $this->publishes([
                             __DIR__ . '/../Resources/config/modules.php' => config_path('modules.php'),
                             __DIR__ . '/../stubs' => base_path('stubs')
                         ], 'base-package');
    }

    private function loadModulesFolder()
    {
        spl_autoload_register(function ($class) {
            $prefix = 'Modules\\';
            $baseDir = base_path('modules/');

            if (strpos($class, $prefix) === 0) {
                $relativeClass = substr($class, strlen($prefix));
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

                if (file_exists($file)) {
                    require_once $file;
                }
            }
        });
    }

    private function bindFacade()
    {
        $this->app->bind('json', function () {
            return new Json();
        });
    }
}
