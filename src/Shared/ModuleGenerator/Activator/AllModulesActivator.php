<?php

declare(strict_types=1);

namespace  BasePackage\Shared\ModuleGenerator\Activator;

use Nwidart\Modules\Contracts\ActivatorInterface;
use Nwidart\Modules\Module;

class AllModulesActivator implements ActivatorInterface
{
    public function enable(Module $module): void
    {
        //
    }

    public function disable(Module $module): void
    {
        //
    }

    public function hasStatus(Module $module, bool $status): bool
    {
        // This method is responsible for checking if given module is enabled (status = true)
        // or disabled (status = false). We want to consider all modules always enabled so we
        // can just return the status here.
        // That way, if someone will ask whether a module is enabled (status = true) this method
        // returns true, otherwise they will ask if module is disabled (status = false) and this
        // method will return false (which is exactly what we need because we consider modules
        // to be always enabled).

        return $status;
    }

    public function setActive(Module $module, bool $active): void
    {
        //
    }

    public function setActiveByName(string $name, bool $active): void
    {
        //
    }

    public function delete(Module $module): void
    {
        //
    }

    public function reset(): void
    {
        //
    }
}
