<?php

namespace App\Models;

use Cache;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'blog_id',
        'created_user_id',
        'name',
        'text'
    ];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }

    public static function booted()
    {
        static::created(function (Article $article) {
            $article->blog->update(['last_article_id' => $article->id]);
            Cache::forget($article->blog->cacheKey());
        });

        static::updated(function (Article $article) {
            Cache::forget(self::staticCacheKey());
            Cache::forget($article->cacheKey());
            Cache::forget($article->blog->cacheKey());
        });

        static::deleted(function (Article $article) {
            Cache::forget(self::staticCacheKey());
            Cache::forget($article->cacheKey());
            Cache::forget($article->blog->cacheKey());
        });
    }

    public function cacheKey(): string
    {
        return sprintf("%s-%s", $this->getTable(), $this->getKey());
    }

    public static function staticCacheKey(?int $key = null): string
    {
        return $key ? "articles-{$key}" : 'articles';
    }

    protected static function newFactory(): ArticleFactory
    {
        return ArticleFactory::new();
    }
}
