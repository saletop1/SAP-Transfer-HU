<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

class TransferController extends Controller
{
    public function showTransferPage()
    {
        return view("transfer");
    }

    public function processTransfer(Request $request)
    {
        // dd($request->all());
        // 1. Validasi input dari form
        $validated = $request->validate([
            'destSloc' => 'required|string',
            'hus'      => 'required|array',  // <-- Diubah menjadi array
            'hus.*'    => 'string',          // Opsional: memastikan setiap item di dalam array adalah string
        ]);

        // 2. Pastikan kredensial SAP ada di sesi Laravel
        if (!session()->has('username') || !session()->has('password')) {
            return back()->withErrors(['transfer' => 'Sesi SAP tidak ditemukan. Silakan login kembali.']);
        }

        try {
            $payload = [
                'hus' => $validated['hus'], // Langsung gunakan array dari hasil validasi
                'destSloc' => $validated['destSloc'],
                'username' => session('username'),
                'password' => session('password'),
            ];
            
            // Alamat API Flask Anda (sebaiknya disimpan di file .env)
            $flaskApiUrl = env('FLASK_SAP_API_URL', 'http://127.0.0.1:8080/api/transfer');

            // 4. Panggil API Flask menggunakan HTTP Client
            $response = Http::timeout(60)->post($flaskApiUrl, $payload);

            // 5. Proses respons dari Flask
            if ($response->successful()) {
                // Jika Flask mengembalikan status 2xx (sukses)
                $data = $response->json();
                return back()->with('success', $data['message'] ?? 'Proses transfer berhasil.');
            } else {
                // Jika Flask mengembalikan error (status 4xx atau 5xx)
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Terjadi kesalahan saat memproses transfer di SAP.';
                return back()->withErrors(['transfer' => $errorMessage]);
            }

        } catch (ConnectionException $e) {
            // Jika koneksi ke Flask gagal total (misal: Flask mati)
            Log::error('Gagal terhubung ke SAP Flask API: ' . $e->getMessage());
            return back()->withErrors(['transfer' => 'Tidak dapat terhubung ke layanan SAP. Silakan coba lagi nanti.']);
        }
    }
}
