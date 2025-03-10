<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    // Menampilkan semua gallery
    public function index()
    {
        $galleries = Gallery::with('user')->get();
    
        if ($galleries->isEmpty()) {
            return response()->json(['message' => 'No galleries found'], 404);
        }
    
        // Debugging: Lihat apakah data benar-benar ada
        return response()->json($galleries, 200);
    }
    
    // Menambahkan gallery baru
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gallery' => 'required|file|mimes:jpeg,png,jpg,mp4,mov,avi|',
        ]);

        $filePath = $request->file('gallery')->store('galleries', 'public');

        $gallery = Gallery::create([
            'user_id' => $request->user_id,
            'gallery' => $filePath,
        ]);

        return response()->json(['message' => 'Gallery uploaded successfully', 'data' => $gallery], 201);
    }

    public function show($id)
    {
        $gallery = Gallery::with('user')->find($id);

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        $gallery->gallery = Storage::url($gallery->gallery);

        return response()->json($gallery, 200);
    }

    // Mengupdate gallery
    public function update(Request $request, $id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        $request->validate([
            'gallery' => 'sometimes|file|mimes:jpeg,png,jpg,mp4,mov,avi|',
        ]);

        if ($request->hasFile('gallery')) {
            Storage::disk('public')->delete($gallery->gallery);
            $filePath = $request->file('gallery')->store('galleries', 'public');
            $gallery->update(['gallery' => $filePath]);
        }

        return response()->json(['message' => 'Gallery updated successfully', 'data' => $gallery], 200);
    }

    // Menghapus gallery
    public function destroy($id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        Storage::disk('public')->delete($gallery->gallery);
        $gallery->delete();

        return response()->json(['message' => 'Gallery deleted successfully'], 200);
    }
}
