<?php

declare(strict_types=1);

namespace  BasePackage\Shared\Provider;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    public function boot()
    {
        HasMany::macro('sync', function (array $data, $primaryKey = 'id') {
            $existingIds = $this->pluck($primaryKey)->toArray(); // Get existing IDs
            $incomingIds = collect($data)->pluck($primaryKey)->filter()->toArray(); // Get incoming IDs

            // Determine IDs to delete
            $idsToDelete = array_diff($existingIds, $incomingIds);
            if (!empty($idsToDelete)) {
                $this->whereIn($primaryKey, $idsToDelete)->delete();
            }

            foreach ($data as $item) {
                if (isset($item[$primaryKey]) && in_array($item[$primaryKey], $existingIds)) {
                    // Update existing record
                    $this->where($primaryKey, $item[$primaryKey])->update(Arr::except($item, [$primaryKey]));
                } else {
                    // Create new record
                    $this->create($item);
                }
            }
        });
    }
}
