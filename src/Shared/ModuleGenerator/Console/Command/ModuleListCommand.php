<?php

declare(strict_types=1);

namespace  BasePackage\Shared\ModuleGenerator\Console\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Nwidart\Modules\Module;

class ModuleListCommand extends Command
{
    protected $name = 'module:list';
    protected $description = 'Show information about all modules';

    private ?array $codeOwnersCache = null;

    public function handle(): int
    {
        $this->line('<fg=green>Modules marked as <fg=red>[WIP]</> are not fully moved to per-module file structure yet</>');
        $this->info('It means that some of their code still lives in e.g. app/, database/, routes/ etc');
        $this->info('We will of course move all the files to their respective module dirs over time');
        $this->newLine();

        $this->table(['Name', 'Description', 'Code Owners'], $this->getRows());

        $this->newLine(2);
        $this->line('<fg=green>Note:</> "Code owners" column is using GitHub usernames. Anyways, you can');
        $this->line('find this information useful to see who can help you with understanding');
        $this->line('the code (or purpose) of each module');
        $this->newLine();

        return self::SUCCESS;
    }

    private function getRows(): array
    {
        $rows = [];

        /** @var Module $module */
        foreach ($this->laravel['modules']->all() as $module) {
            // Use more distinctive color for [WIP] prefixes
            $description = str_replace('[WIP] ', '<fg=red>[WIP]</> ', $module->getDescription());

            $path = '/modules/' . Str::after($module->getPath(), 'modules/') . '/';

            $rows[] = [
                $module->getName(),
                $description,
                $this->getCodeOwnersForPath($path) ?? 'Unknown',
            ];
        }

        return $rows;
    }

    private function getCodeOwnersForPath(string $path): ?string
    {
        if ($this->codeOwnersCache === null) {
            $this->codeOwnersCache = $this->parseCodeownersFile();
        }

        return $this->codeOwnersCache[$path] ?? null;
    }

    private function parseCodeownersFile(): array
    {
        $codeownersPath = base_path('.github/CODEOWNERS');
        if (file_exists($codeownersPath) === false) {
            return [];
        }

        $result = [];
        foreach (file($codeownersPath) as $line) {
            $line = trim($line);

            if (str_starts_with($line, '#') || $line === '') {
                // Skip comments and empty lines
                continue;
            }

            $segments = explode(' ', $line, 2);

            $path = $segments[0];
            if (count($segments) !== 2) {
                $owners = null;
            } else {
                $owners = $segments[1];
            }

            $result[$path] = $owners;
        }

        return $result;
    }
}
