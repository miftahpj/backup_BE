<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    // Menampilkan semua file
    public function index()
    {
        $files = File::all()->map(function ($file) {
            $file->url = asset($file->path); // Menggunakan 'path' sesuai tabel
            return $file;
        });

        return response()->json($files);
    }

    // Menyimpan file baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filename' => 'required|image|mimes:jpeg,png,jpg,gif|',
            'article_item_id' => 'required|exists:article_items,id', // Menyesuaikan dengan tabel
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('filename');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('storage/article_items', $filename, 'public'); // Simpan ke storage

        $newFile = File::create([
            'filename' => $filename,
            'path' => $path, // Simpan path sesuai struktur tabel
            'mime_type' => $file->getClientMimeType(), // Simpan tipe file
            'article_item_id' => $request->article_item_id, // Simpan ID artikel item
        ]);

        return response()->json([
            'message' => 'File berhasil diunggah',
            'file' => $newFile,
            'url' => asset($newFile->path), // Menghasilkan URL file
        ], 201);
    }

    // Menampilkan file berdasarkan ID
    public function show($id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }

        $file->url = asset($file->path);
        return response()->json($file);
    }

    // Mengupdate file
    public function update(Request $request, $id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'filename' => 'sometimes|image|mimes:jpeg,png,jpg,gif|',
            'article_item_id' => 'sometimes|exists:article_items,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            if ($request->hasFile('filename')) {
                Storage::disk('public')->delete($file->path); // Hapus file lama
                $newFile = $request->file('filename');
                $newFilename = time() . '_' . $newFile->getClientOriginalName();
                $newPath = $newFile->storeAs('storage/article_items', $newFilename, 'public');

                $file->filename = $newFilename;
                $file->path = $newPath;
                $file->mime_type = $newFile->getClientMimeType();
            }

            if ($request->article_item_id) {
                $file->article_item_id = $request->article_item_id;
            }

            $file->save();

            return response()->json([
                'message' => 'File berhasil diperbarui',
                'file' => $file,
                'url' => asset($file->path),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui file', 'message' => $e->getMessage()], 500);
        }
    }

    // Menghapus file
    public function destroy($id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }

        try {
            Storage::disk('public')->delete($file->path);
            $file->delete();

            return response()->json(['message' => 'File berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus file', 'message' => $e->getMessage()], 500);
        }
    }
}
