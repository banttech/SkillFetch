<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value', 'label'];

    // ── Static helpers ────────────────────────────────────────────────────────

    /**
     * Get a setting value by key, with optional default.
     * Cached for 60 minutes to avoid repeated DB hits.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("app_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value by key. Clears the cache for that key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("app_setting_{$key}");
    }

    // ── Specific setting accessors ────────────────────────────────────────────

    /**
     * Returns the configured attempt cooldown window in hours.
     * Default: 24 hours.
     */
    public static function testAttemptWindowHours(): int
    {
        return (int) static::get('test_attempt_window_hours', 24);
    }
}