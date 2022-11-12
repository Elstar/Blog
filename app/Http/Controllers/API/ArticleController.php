<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function store(ArticleRequest $request)
    {
        $article = Article::create($request->validated());
        return response()->json(['success' => true, 'data' => $article->toArray()]);
    }

    public function update(ArticleRequest $request, int $id)
    {
        $article = Article::find($id);
        $validated = $request->validated();
        if (!empty($validated)) {
            $article->update($validated);
            $article->refresh();
        }
        return response()->json(['success' => true, 'data' => $article->toArray()]);
    }

    public function destroy(ArticleRequest $request, int $id)
    {
        $article = Article::find($id);
        $article->delete();
        return response()->json(['success' => true, 'message' => 'Article successfully deleted']);
    }
}
