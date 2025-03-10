<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class StatusController extends Controller
{
    // Mendapatkan semua status
    public function index()
    {
        $statuses = Status::all();

        return response()->json([
            'message' => 'List of statuses',
            'data' => $statuses
        ], 200);
    }

    // Menyimpan status baru
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255|unique:status,title',
            ]);

            $status = Status::create($validatedData);

            return response()->json([
                'message' => 'Status created successfully',
                'data' => $status
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Menampilkan status berdasarkan ID
    public function show($id)
    {
        try {
            $status = Status::findOrFail($id);
            return response()->json([
                'message' => 'Status found',
                'data' => $status
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Status not found'
            ], 404);
        }
    }

    // Mengupdate status berdasarkan ID
    public function update(Request $request, $id)
    {
        try {
            $status = Status::findOrFail($id);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255|unique:status,title,' . $id,
            ]);

            $status->update($validatedData);

            return response()->json([
                'message' => 'Status updated successfully',
                'data' => $status
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Status not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Menghapus status berdasarkan ID
    public function destroy($id)
    {
        try {
            $status = Status::findOrFail($id);
            $status->delete();

            return response()->json([
                'message' => 'Status deleted successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Status not found'
            ], 404);
        }
    }
}
