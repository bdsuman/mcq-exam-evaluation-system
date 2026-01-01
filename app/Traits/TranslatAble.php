<?php

namespace App\Traits;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;

trait TranslatAble
{
    // Boot Trait Hooks
    public static function bootTranslatable()
    {
        static::updated(fn($model) => $model->clearAllTranslationsCache());
        static::saved(fn($model) => $model->refreshAllTranslationsCache());
        static::deleted(fn($model) => $model->clearAllTranslationsCache());
    }

    // Relationship
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    // -----------------------------
    // 🔍 UNIVERSAL SEARCH SCOPE
    // -----------------------------
    public function scopeSearchTranslations($query, ?string $search = null, ?array $keys = [])
    {
        $lang = app('language');

        return $query->when($search, function ($q) use ($search, $lang, $keys) {
            $q->whereHas('translations', function ($sub) use ($search, $lang, $keys) {
                $sub->where('language', $lang)
                    ->where(function ($inner) use ($keys, $search) {
                        foreach ($keys as $key) {
                            $inner->orWhere(function ($sq) use ($key, $search) {
                                $sq->where('key', $key)
                                    ->where('value', 'ILIKE', "%{$search}%");
                            });
                        }
                    });
            });
        });
    }

    // -----------------------------
    // 🧠 TRANSLATION HELPERS
    // -----------------------------
    public function getTranslation(string $key, ?string $lang = null): ?string
    {
        $lang = $lang ?: app('language');
        $cacheKey = $this->getTranslationCacheKey($key, $lang);

        return Cache::remember($cacheKey, now()->addHours(2), function () use ($key, $lang) {
            if ($this->relationLoaded('translations')) {
                return $this->translations
                    ->where('language', $lang)
                    ->where('key', $key)
                    ->first()?->value;
            }

            return $this->translations()
                ->where('language', $lang)
                ->where('key', $key)
                ->value('value');
        });
    }

    public function setTranslation(string $key, string $value, ?string $lang = null): Translation
    {
        $lang = $lang ?: app('language');

        $translation = $this->translations()->updateOrCreate(
            ['language' => $lang, 'key' => $key],
            ['value' => $value]
        );

        $this->refreshTranslationCache($key, $lang);

        return $translation;
    }

    public function getAllTranslations(?string $lang = null): array
    {
        $lang = $lang ?: app('language');
        $cacheKey = $this->getTranslationCacheKey('all', $lang);

        return Cache::remember($cacheKey, now()->addHours(2), function () use ($lang) {
            if ($this->relationLoaded('translations')) {
                return $this->translations
                    ->where('language', $lang)
                    ->pluck('value', 'key')
                    ->toArray();
            }

            return $this->translations()
                ->where('language', $lang)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    public function scopeWithTranslations($query, ?string $lang = null)
    {
        $lang = $lang ?: app('language');

        return $query->with(['translations' => fn($q) => $q->where('language', $lang)]);
    }

    public function loadTranslations(?string $lang = null): static
    {
        $lang = $lang ?: app('language');

        return $this->load(['translations' => fn($q) => $q->where('language', $lang)]);
    }
    // -----------------------------
    // ⚙️ CACHE HANDLING
    // -----------------------------
    protected function getTranslationCacheKey(string $key, string $lang): string
    {
        return "translatable:{$this->getTable()}:{$this->id}:{$lang}:{$key}";
    }

    public function clearTranslationCache(string $key, ?string $lang = null): void
    {
        $lang = $lang ?: app('language');
        Cache::forget($this->getTranslationCacheKey($key, $lang));
    }

    public function clearAllTranslationsCache(?string $lang = null): void
    {
        $lang = $lang ?: app('language');
        Cache::forget($this->getTranslationCacheKey('all', $lang));
    }

    protected function refreshTranslationCache(string $key, ?string $lang = null): void
    {
        $lang = $lang ?: app('language');
        $value = $this->translations()
            ->where('language', $lang)
            ->where('key', $key)
            ->value('value');

        Cache::put($this->getTranslationCacheKey($key, $lang), $value, now()->addHours(2));
        $this->refreshAllTranslationsCache($lang);
    }

    public function refreshAllTranslationsCache(?string $lang = null): void
    {
        $lang = $lang ?: app('language');
        $all = $this->translations()
            ->where('language', $lang)
            ->pluck('value', 'key')
            ->toArray();

        Cache::put($this->getTranslationCacheKey('all', $lang), $all, now()->addHours(2));
    }
}
