<?php

namespace App\Http\Controllers;

use App\Models\ReachUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReachUsController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Validasi input
        $request->validate([
            'whatsapp' => 'required|numeric|digits_between:10,15',
            'username' => 'nullable|string|max:255',
            'message'  => 'required|string|max:1000',
        ]);

        // Kirim notifikasi ke WhatsApp via Fonnte
        $response = Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN'),
        ])->post('https://api.fonnte.com/send', [
            'target'      => $request->whatsapp,
            'message'     => "Terima kasih {$request->username} telah menghubungi Rekhatama Training Center 

Rekhatama Training Center dapat membantumu dengan :
- Training & certification
- IT Consultant
- Software Development

Jangan lupa untuk terus upgrade skill kamu!✨

Apakah ada yang bisa dibantu?",
            'countryCode' => '62',
        ]);

        // Log response dari API untuk debugging jika ada masalah
        Log::info('Fonnte Response:', ['status' => $response->status(), 'body' => $response->body()]);

        // Cek apakah pengiriman berhasil
        if ($response->successful()) {
            // Simpan ke database hanya jika berhasil
            $data = ReachUs::create([
                'whatsapp' => $request->whatsapp,
                'username' => $request->username,
                'message'  => $request->message,
            ]);

            return response()->json([
                'message' => 'Pesan terkirim!',
                'data'    => $data,
            ], 200);
        } else {
            return response()->json([
                'error'   => 'Gagal mengirim pesan. Coba lagi.',
                'details' => $response->body(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        return $this->sendMessage($request);
    }

    // GET: Ambil semua data untuk ditampilkan di frontend
    public function index()
    {
        $messages = ReachUs::orderBy('created_at', 'desc')->paginate(30);
    
        return response()->json([
            'success' => true,
            'data'    => $messages,
        ], 200);
    }

    // DELETE: Hapus data berdasarkan ID
    public function destroy($id)
    {
        $message = ReachUs::find($id);

        if (!$message) {
            return response()->json([
                'error' => 'Data tidak ditemukan!',
            ], 404);
        }

        $message->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus!',
        ], 200);
    }
}
