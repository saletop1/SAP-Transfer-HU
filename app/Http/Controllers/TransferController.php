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
        // Validasi akan tetap berjalan seperti biasa
        $validated = $request->validate([
            'hus'      => 'required|array|min:1',
            'hus.*'    => 'string|distinct',
            'destSloc' => 'required|string',
        ]);
        
        $sapUsername = session('sap_username');
        $sapPassword = session('sap_password');

        if (!$sapUsername || !$sapPassword) {
            // Redirect kembali dengan pesan error
            return redirect()->route('transfer')
                ->with('error', 'Sesi kredensial SAP tidak ditemukan. Silakan login kembali.');
        }

        try {
            $apiUrl = 'http://192.168.90.27:5006/api/transfer';

            $response = Http::timeout(30)->withHeaders([
                'Accept' => 'application/json',
                'X-SAP-Username' => $sapUsername,
                'X-SAP-Password' => $sapPassword,
            ])->post($apiUrl, [
                'hus' => $validated['hus'],
                'destSloc' => $validated['destSloc'],
            ]); 
            $response->throw(); // Lempar exception jika status 4xx atau 5xx

            // JIKA SUKSES: Redirect kembali dengan pesan sukses
            $result = $response->json();
            return redirect()->route('transfer')
                ->with('success', $result['message'] ?? 'Semua HU berhasil ditransfer.');

        } catch (ConnectionException $e) {
            Log::error('Gagal terhubung ke API Flask: ' . $e->getMessage());
            return redirect()->route('transfer')
                ->with('error', 'Tidak dapat terhubung ke server pemrosesan. Hubungi administrator.');

        } catch (RequestException $e) {
            Log::error('API Flask mengembalikan response error: ' . $e->getMessage());
            $errorData = $e->response->json();
            $errorMessage = $errorData['message'] ?? 'Terjadi kegagalan saat transfer.';
            
            // Kirim detail HU yang gagal jika ada
            $failedHusDetails = $errorData['details']['failed_hus'] ?? null;

            return redirect()->route('transfer')
                ->with('error', $errorMessage)
                ->with('failed_hus', $failedHusDetails);
                
        } catch (\Exception $e) {
            Log::error('Error tak terduga: ' . $e->getMessage());
            return redirect()->route('transfer')
                ->with('error', 'Terjadi kesalahan internal pada server.');
        }
    }
}