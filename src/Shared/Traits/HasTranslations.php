<?php

declare(strict_types=1);

namespace  BasePackage\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;
use  BasePackage\Shared\Model\Translation;

trait HasTranslations
{
    protected array $pendingTranslations = [];

    protected static function bootHasTranslations()
    {
        static::addGlobalScope('withTranslations', function (Builder $builder) {
            $builder->with('translations');
        });

        static::saved(function ($model) {
            $model->savePendingTranslations();
        });
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function getAttribute($key)
    {
        // Check if this is a translatable field
        if ($this->isTranslatableAttribute($key)) {
            if (!empty($translation = $this->getTranslation($key))) {
                return $translation;
            } else {
                $locale = $locale ?? app()->getLocale();
                if (
                    !empty($translation = parent::getAttribute($key.'_'.$locale))
                    || !empty($translation = parent::getAttribute($locale.'_'.$key))
                    || !empty($translation = parent::getAttribute($key))
                )
                {
                    $this->setTranslation($key, $locale, $translation);
                    $this->translations = $this->translations()->get();
                    return $this->getTranslation($key);
                }
            }
        }

        // Otherwise, fall back to the parent method
        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        if ($this->isTranslatableAttribute($key)) {
            $this->pendingTranslations[$key] = $value;

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    protected function savePendingTranslations()
    {
        if (!empty($this->pendingTranslations)) {
            foreach ($this->pendingTranslations as $field => $translations) {
                if (is_array($translations)) {
                    foreach ($translations as $locale => $content) {
                        $this->setTranslation($field, $locale, $content);
                    }
                } else {
                    $locale = app()->getLocale();
                    $this->setTranslation($field, $locale, $translations);
                }
            }
            // Clear pending translations after saving them.
            unset($this->pendingTranslations);
        }
    }


    public function getTranslation(string $field, string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $contentLocale = $this->translations
            ->where('field', $field)
            ->where('locale', $locale);
        $fallbackLocale = $this->translations
            ->where('field', $field)
            ->where('locale', 'en');

        return $contentLocale->value('content') ?? $fallbackLocale->value('content') ?? '';
    }

    public function setTranslation(string $field, string $locale, string $content): void
    {
        $this->translations()->updateOrCreate(
            ['field' => $field, 'locale' => $locale],
            ['content' => $content]
        );
    }

    public function isTranslatableAttribute($key): bool
    {
        return in_array($key, $this->translatable ?? []);
    }

    public function scopeWhereTranslatable($query, $field, $value)
    {
        if ($this->isTranslatableAttribute($field)) {
            return $query->whereHas('translations', function ($q) use ($field, $value) {
                $q->where('field', $field)
                    ->where('content', 'LIKE', '%' . $value . '%');
            });
        }

        return $query->where($field, 'LIKE', '%' . $value . '%');
    }
}
