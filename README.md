# Laravel Simple Cache Trait

A lightweight Laravel trait that simplifies working with cache keys and tagged cache storage.
It automatically generates stable hashed cache keys, handles nested arrays, sorts key data, and supports cache tags across all Laravel versions starting from **8.x**.

## Features

- Automatic cache key generation using SHA-256
- Deterministic sorting of array keys before hashing
- Tagged cache support (if the store supports it)
- Graceful fallback when caching fails
- Easy cache flushing for specific tag groups
- Works on Laravel **8+**

## Installation

```bash
composer require dv0vd/laravel-simple-cache
```

## Usage
```php
use Dv0vD\LaravelSimpleCache\CacheTrait;

class ProductRepository
{
    use CacheTrait;

    public function get(int $id)
    {
        return $this->rememberCache(
            callback: fn () => Product::find($id),
            keys: ['id' => $id],
        );
    }
}
```

Tag Constants (optional):
```php
class ProductRepository
{
    use CacheTrait;

    private const CACHE_MAIN_TAG = 'products';
    private const CACHE_TAGS = ['repository', 'models'];

    public function get(int $id)
    {
        return $this->rememberCache(
            callback: fn () => Product::find($id),
            keys: ['id' => $id],
        );
    }
}
```

Clearing cache:
```php
$this->clearCache();
```
```php
$this->clearCache(['products']);
```

## Available Methods

### rememberCache(callable $callback, array $keys = [], array $tags = [], ?int $ttl = null): mixed
Stores or retrieves a value from cache:
- Automatically generates a cache key based on the calling class, method, and the `$keys` array (sorted & hashed).
- Uses tags if the cache driver supports them.
- Logs and silently falls back to the callback if caching fails.
- `$ttl` (in seconds) is optional. Defaults to `cache.ttl` from your config.

### clearCache(array $tags = []): void
Flushes all cache entries for the provided tags or default tags.
- By default, tags are taken from the constants `CACHE_MAIN_TAG` and `CACHE_TAGS`.
- If you pass tags as a parameter, they will override the default constants.
- Only cache entries with tags are cleared.
- If no tags are found, the method does nothing to avoid accidental data loss.