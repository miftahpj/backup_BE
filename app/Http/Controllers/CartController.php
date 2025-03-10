<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    // Ambil semua item di cart
    public function index()
    {
        $cartItems = Cart::with('course')->get();
        return response()->json($cartItems, 200);
    }

    // Tambah item ke cart
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'course_ids' => 'required|array', // Mengubah menjadi array
                'course_ids.*' => 'integer|exists:course,id', // Validasi setiap course_id
                'name' => 'required|string|max:255',
                'purpose' => 'required|string|max:255',
                'whatsapp' => 'required|string|max:15',
            ]);
    
            $cartItems = [];
            foreach ($validatedData['course_ids'] as $courseId) {
                $cartItems[] = Cart::create([
                    'course_id' => $courseId,
                    'name' => $validatedData['name'],
                    'purpose' => $validatedData['purpose'],
                    'whatsapp' => $validatedData['whatsapp'],
                ]);
            }
    
            return response()->json(['message' => 'Courses added to cart', 'data' => $cartItems], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
    

    // Hapus item dari cart
    public function destroy($id)
    {
        try {
            $cart = Cart::findOrFail($id);
            $cart->delete();

            return response()->json(['message' => 'Item removed from cart'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Item not found'], 404);
        }
    }

    // Hapus semua item dari cart
    public function clear()
    {
        Cart::truncate();
        return response()->json(['message' => 'Cart cleared'], 200);
    }
}
