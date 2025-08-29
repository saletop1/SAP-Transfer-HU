<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;

class LoginController extends Controller
{
    public function ShowLoginForm()
    {
        return view("login");
    }

    public function login(Request $request)
    {
        // 1. Validasi input
        // Laravel secara otomatis akan mengembalikan respons JSON jika validasi gagal pada request AJAX.
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // 2. Panggil API Login di Flask
            $flaskApiUrl = env('FLASK_SAP_API_URL', 'http://127.0.0.1:8080') . '/api/login';

            $response = Http::timeout(30)->post($flaskApiUrl, [
                'username' => $credentials['username'],
                'password' => $credentials['password'],
            ]);

            // 3. Cek respons dari Flask
            if (!$response->successful()) {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Username atau Password SAP tidak valid.';
                
                // ## PERBAIKAN 1: Mengembalikan JSON untuk error otentikasi ##
                // Daripada redirect, kita kirim respons JSON dengan status 401 (Unauthorized).
                return response()->json(['message' => $errorMessage], 401);
            }

            // --- JIKA OTENTIKASI SAP BERHASIL ---

            // 4. Simpan kredensial SAP ke sesi Laravel
            session([
                'sap_username' => $credentials['username'],
                'sap_password' => $credentials['password'], // Catatan: Menyimpan password plaintext di sesi tidak direkomendasikan. Pertimbangkan alternatif lain jika keamanan sangat krusial.
            ]);
            
            // 5. Lakukan proses login di dalam sistem Laravel
            $user = User::firstOrCreate(
                ['email' => $credentials['username'] . '@example.com'],
                [
                    'name' => $response->json('user', $credentials['username']),
                    'password' => bcrypt(Str::random(16)) // Menggunakan Str helper
                ]
            );

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            // ## PERBAIKAN 2: Mengembalikan JSON untuk login sukses ##
            // Daripada redirect, kirim URL tujuan dalam format JSON.
            // JavaScript yang akan menangani proses redirect di sisi klien.
            return response()->json([
                'message' => 'Login berhasil!',
                'redirect_url' => session()->pull('url.intended', '/transfer') // Mengambil URL intended atau default ke '/transfer'
            ]);

        } catch (ConnectionException $e) {
            Log::error('Koneksi ke API SAP Gagal: ' . $e->getMessage());
            
            // ## PERBAIKAN 3: Mengembalikan JSON untuk error koneksi ##
            // Kirim respons JSON dengan status 503 (Service Unavailable).
            return response()->json(['message' => 'Tidak dapat terhubung ke layanan otentikasi.'], 503);
        }
    }

    /**
     * Memproses logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Hapus juga sesi kredensial SAP
        $request->session()->forget(['sap_username', 'sap_password']);

        return redirect('/');
    }
}
