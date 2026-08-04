<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function (self $model) {
            $source = $model->getSlugSource();
            $slug = $model->slug;

            if (blank($slug) && blank($source)) {
                $slug = 'item-' . Str::lower(Str::random(8));
            } elseif (blank($slug)) {
                $slug = Str::slug((string) $source);
            } else {
                $slug = Str::slug((string) $slug);
            }

            // Non-Latin titles can yield an empty slug from Str::slug()
            if (blank($slug)) {
                $slug = 'item-' . Str::lower(Str::random(8));
            }

            $model->slug = static::makeSlugUnique($model, $slug);
        });
    }

    protected function getSlugSource(): ?string
    {
        return $this->title ?? $this->name ?? null;
    }

    protected static function makeSlugUnique(self $model, string $slug): string
    {
        $base = $slug;
        $i = 1;

        while (true) {
            // Include soft-deleted rows so we never collide with the unique DB index
            $query = static::query();
            if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class), true)) {
                $query->withTrashed();
            }
            $query->where('slug', $slug);
            if ($model->exists) {
                $query->where($model->getKeyName(), '!=', $model->getKey());
            }
            if (! $query->exists()) {
                break;
            }
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Prefer slug; fall back to id so route() never throws on blank slugs.
     */
    public function getRouteKey(): mixed
    {
        $key = $this->getAttribute($this->getRouteKeyName());

        return filled($key) ? $key : $this->getKey();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?? $this->getRouteKeyName();
        $query = static::query();

        if (is_numeric($value)) {
            return $query->where('id', $value)->first();
        }

        $found = $query->where($field, $value)->first();
        if ($found) {
            return $found;
        }

        // Fallback: some older rows may have been linked by id in admin bookmarks
        if (ctype_digit((string) $value)) {
            return $query->where('id', (int) $value)->first();
        }

        return null;
    }
}
