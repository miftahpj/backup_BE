<?php

namespace App\Http\Controllers;

use App\Models\ArticleItem;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ArticleItemController extends Controller
{
    public function index()
    {
        $items = ArticleItem::with('author', 'article', 'files')->paginate(100);
        return response()->json($items, 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'article_id' => 'required|exists:articles,id',
                'author_id' => 'required|exists:users,id',
                'title' => 'required|string|max:255',
                'image' => 'nullable|array', // Izinkan array untuk gambar
                'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi tiap file dalam array
                'description' => 'nullable|string',
                'content' => 'required|string',
                'template' => 'nullable|string',
            ]);
    
            // Simpan article item
            $articleItem = ArticleItem::create($validatedData);
    
            // Simpan semua gambar jika ada
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $file) {
                    $path = $file->store('article_items', 'public');
    
                    File::create([
                        'filename' => $file->getClientOriginalName(),
                        'path' => 'storage/' . $path,
                        'mime_type' => $file->getClientMimeType(),
                        'article_item_id' => $articleItem->id
                    ]);
                }
            }
    
            return response()->json($articleItem->load('files'), 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
    

    public function show($id)
    {
        try {
            $articleItem = ArticleItem::with('author', 'article', 'files')->findOrFail($id);
            return response()->json($articleItem, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Article Item not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $articleItem = ArticleItem::findOrFail($id);

            $validatedData = $request->validate([
                'article_id' => 'exists:articles,id',
                'author_id' => 'exists:users,id',
                'title' => 'string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|',
                'description' => 'nullable|string',
                'content' => 'string',
                'template' => 'nullable|string',
            ]);

            $articleItem->update($validatedData);

            
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = $file->store('article_items', 'public');

                $oldFile = File::where('article_item_id', $articleItem->id)->first();
                if ($oldFile) {
                    Storage::delete(str_replace('storage/', 'public/', $oldFile->path));
                    $oldFile->delete();
                }

                // Simpan file baru di tabel files
                File::create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => 'storage/' . $path,
                    'mime_type' => $file->getClientMimeType(),
                    'article_item_id' => $articleItem->id
                ]);
            }

            return response()->json($articleItem->load('files'), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Article Item not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $articleItem = ArticleItem::findOrFail($id);

            // Hapus file dari storage dan database
            $files = File::where('article_item_id', $articleItem->id)->get();
            foreach ($files as $file) {
                Storage::delete(str_replace('storage/', 'public/', $file->path));
                $file->delete();
            }

            $articleItem->delete();
            return response()->json(['message' => 'Article Item deleted'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Article Item not found'], 404);
        }
    }
}
