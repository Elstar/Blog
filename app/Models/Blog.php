<?php

namespace App\Models;

use Cache;
use Database\Factories\BlogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_article_id',
        'name',
        'text'
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'blog_id');
    }

    public function lastArticle(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'last_article_id');
    }

    protected static function newFactory(): BlogFactory
    {
        return BlogFactory::new();
    }

    public static function booted()
    {
        static::created(function (Blog $blog) {
            Cache::forget(self::staticCacheKey());
        });

        static::updated(function (Blog $blog) {
            Cache::forget(self::staticCacheKey());
            Cache::forget($blog->cacheKey());
        });

        static::deleted(function (Blog $blog) {
            Cache::forget(self::staticCacheKey());
            Cache::forget($blog->cacheKey());
        });
    }

    public function cacheKey(): string
    {
        return sprintf("%s-%s", $this->getTable(), $this->getKey());
    }

    public static function staticCacheKey(?int $key = null): string
    {
        return $key ? "blogs-{$key}" : 'blogs';
    }
}
