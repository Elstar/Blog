<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Cache;

class ArticleController extends Controller
{

    public function store(ArticleRequest $request, int $blog)
    {
        $validated = $request->validated();
        $article = Article::create($validated);
        return response()->json(['success' => true, 'article' => $article->toArray(), 'action' => 'store']);
    }

    public function show(int $blog, int $id)
    {
        $article = Cache::rememberForever(Article::staticCacheKey($id), function () use ($id) {
            return Article::with('blog')->findOrFail($id);
        });
        return view('article.show', [
            'article' => $article
        ]);
    }

    public function update(ArticleRequest $request, int $blog, int $article)
    {
        $article = Article::findOrFail($article);
        $validated = $request->validated();
        $article->update($validated);
        $article->refresh();
        return response()->json(['success' => true, 'article' => $article->toArray(), 'action' => 'update']);
    }

    public function get(int $blog, int $article)
    {
        $article = Cache::rememberForever(Article::staticCacheKey($article), function () use ($article) {
            return Article::with('blog')->findOrFail($article);
        });
        return response()->json(['success' => true, 'article' => $article->toArray()]);
    }

    public function delete(ArticleRequest $request, int $blog, Article $article)
    {
        return response()->json(['success' => $article->delete(), 'article' => $article]);
    }
}
