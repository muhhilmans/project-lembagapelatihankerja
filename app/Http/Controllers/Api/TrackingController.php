<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Traits\ApiResponse;

class TrackingController extends Controller
{
    use ApiResponse;

    /**
     * API untuk mengambil titik kordinat pekerja (Live Maps)
     */
    public function getLocations(Request $request)
    {
        try {
            // Ambil user dengan role 'pembantu' yang aktif dan memiliki titik kordinat
            $workers = User::whereHas('roles', function($q) {
                    $q->where('name', 'pembantu');
                })
                ->where('is_active', true)
                ->with(['servantDetails' => function($q) {
                    $q->select('user_id', 'latitude', 'longitude', 'working_status', 'updated_at', 'photo', 'gender', 'province', 'regency')
                      ->whereNotNull('latitude')
                      ->whereNotNull('longitude');
                }])
                ->get();

            $locations = $workers->map(function ($worker) {
                $details = $worker->servantDetails;

                if (!$details) return null;

                // Logika Indikator Online: Jika update kordinat terakhir <= 15 menit yang lalu
                $lastSeen = $details->updated_at;
                $isOnline = false;

                if ($lastSeen) {
                    $isOnline = Carbon::now()->diffInMinutes($lastSeen) <= 15;
                }

                return [
                    'user_id' => $worker->id,
                    'name' => $worker->name,
                    'photo' => $details->photo ? asset("storage/img/photo/{$details->photo}") : null,
                    'gender' => $details->gender,
                    'location_text' => "{$details->regency}, {$details->province}",
                    'latitude' => $details->latitude,
                    'longitude' => $details->longitude,
                    'status_ketersediaan' => $details->working_status ? 'Sedang Bekerja' : 'Tersedia',
                    'last_seen' => $lastSeen,
                    'is_online' => $isOnline
                ];
            })->filter()->values(); // Hapus nilai null

            return $this->successResponse($locations, 'Data koordinat pekerja berhasil dimuat untuk Peta.');

        } catch (\Throwable $th) {
            Log::error("Error TrackingController getLocations: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat memuat titik lokasi.', [], 500);
        }
    }
}
