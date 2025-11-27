<?php

declare(strict_types=1);

namespace Dv0vD\LaravelSimpleCache;

use JsonException;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

trait CacheTrait
{
    protected function clearCache(array $tags = []): void
    {
        if ($this->supportsTags()) {
            if (count($tags) == 0) {
                $tags = $this->getTags();
            }

            if (count($tags) > 0) {
                Cache::tags($tags)->flush();
            }
        }
    }

    /**
     * @param string[] $keys
     * @param string[] $tags
     */
    protected function rememberCache(
        callable $callback,
        array $keys = [],
        array $tags = [],
        ?int $ttl = null,
    ): mixed {
        $ttl ??= config('cache.ttl');

        if ($this->supportsTags()) {
            $tags = $this->getTags();
            $cache = Cache::tags($tags);
        } else {
            $cache = Cache::store();
        }

        try {
            return $cache->remember(
                key: $this->getCacheKey(
                    prefix: [$this->getCalledClassName(), $this->getCalledFunctionName()],
                    data: $keys,
                ),
                ttl: $ttl,
                callback: $callback
            );
        } catch (Throwable $e) {
            Log::error("Unknown error during remembering cache: {$e->getMessage()}");

            return $callback();
        }
    }

    /**
     * @param string[] $prefix
     * @throws JsonException
     */
    private function getCacheKey(array $prefix = [], array $data = []): string
    {
        $this->sortKeys($data);

        return implode('|', $prefix) . '|' . hash('sha256', json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function getCalledClassName(): string
    {
        return Str::camel(Arr::last(explode('\\', Arr::get(debug_backtrace(), '2.class'))));
    }

    private function getCalledFunctionName(): string
    {
        return Str::camel(Arr::get(debug_backtrace(), '2.function', ''));
    }

    private function getTags(): array
    {
        return array_merge(
            defined('static::CACHE_MAIN_TAG') ? [static::CACHE_MAIN_TAG] : [],
            defined('static::CACHE_TAGS') && is_array(static::CACHE_TAGS) ? static::CACHE_TAGS : [],
        );
    }

    private function sortKeys(array &$array): void
    {
        ksort($array);
        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->sortKeys($value);
            }
        }
        unset($value);
    }

    private function supportsTags(): bool
    {
        return Cache::store()->getStore() instanceof TaggableStore;
    }
}
