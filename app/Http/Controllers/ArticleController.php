<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{
    public function index() {
        $articles = Article::with('author')->get();
        return response()->json($articles, 200);
    }

    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'author_id' => 'required|exists:users,id',
                'article' => 'required|unique:articles,article'
            ]);

            $article = Article::create($validatedData);
            return response()->json($article, 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function show($id) {
        try {
            $article = Article::with('author', 'items')->findOrFail($id);
            return response()->json($article, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Article not found'], 404);
        }
    }

    public function update(Request $request, $id) {
        try {
            $article = Article::findOrFail($id);

            $validatedData = $request->validate([
                'author_id' => 'required|exists:users,id',
                'article' => 'required|unique:articles,article,' . $article->id
            ]);

            $article->update($validatedData);
            return response()->json($article, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Article not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy($id) {
        try {
            $article = Article::findOrFail($id);
            $article->delete();
            return response()->json(['message' => 'Article deleted'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Article not found'], 404);
        }
    }

    public function getArticleItems($id) {
        try {
            $article = Article::with('items')->findOrFail($id);
            return response()->json($article, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Article not found'], 404);
        }
    }
}