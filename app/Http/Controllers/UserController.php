<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user.
     */
    public function index()
    {
        $users = User::all();
        Log::info('User data:', $users->toArray());
        return response()->json($users, 200);
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,content_creator,user'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : null,
            'role' => $request->role,
        ]);

        return response()->json($user, 201);
    }

    /**
     * Tampilkan detail user tertentu.
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user, 200);
    }

    /**
     * Perbarui user.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|nullable|email|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|string|min:6',
            'role' => 'sometimes|required|in:admin,content_creator,user'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'role' => $request->role ?? $user->role,
        ]);

        return response()->json($user, 200);
    }

    /**
     * Hapus user.
     */
    public function destroy($id)
    {
        Log::info("Mencoba menghapus user dengan ID: $id");
    
        $user = User::find($id);
        if (!$user) {
            Log::error("User dengan ID $id tidak ditemukan!");
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $user->delete();
        Log::info("User dengan ID $id berhasil dihapus");
    
        return response()->json(['message' => 'User deleted'], 200);
    }
}
