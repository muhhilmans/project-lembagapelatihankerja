<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Menampilkan halaman tracking lokasi pembantu
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil semua pembantu yang memiliki data lokasi (latitude dan longitude)
        $servants = User::role('pembantu')
            ->whereHas('servantDetails', function ($query) {
                $query->whereNotNull('latitude')
                      ->whereNotNull('longitude');
            })
            ->with(['servantDetails.profession'])
            ->get();

        return view('cms.tracking.index', compact('servants'));
    }

    /**
     * Mengambil data lokasi pembantu secara realtime (JSON)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocations()
    {
        // Ambil semua pembantu yang memiliki data lokasi (latitude dan longitude)
        $servants = User::role('pembantu')
            ->whereHas('servantDetails', function ($query) {
                $query->whereNotNull('latitude')
                      ->whereNotNull('longitude');
            })
            ->with(['servantDetails.profession'])
            ->get();

        // Format data untuk response JSON
        $data = $servants->map(function ($servant) {
            return [
                'id' => $servant->id,
                'name' => $servant->name,
                'email' => $servant->email,
                'photo' => $servant->servantDetails->photo 
                    ? route('getImage', ['path' => 'photo', 'imageName' => $servant->servantDetails->photo]) 
                    : null,
                'profession' => $servant->servantDetails->profession->name ?? 'Unknown',
                'lat' => $servant->servantDetails->latitude,
                'lng' => $servant->servantDetails->longitude,
                'phone' => $servant->servantDetails->phone,
                'address' => ($servant->servantDetails->regency ?? '-') . ', ' . ($servant->servantDetails->province ?? '-'),
                'status' => $servant->servantDetails->working_status ?? 'Available', // Asumsi ada status kerja
                'last_seen' => $servant->servantDetails->updated_at ? $servant->servantDetails->updated_at->diffForHumans() : 'Belum pernah online',
                'is_online' => $servant->servantDetails->updated_at && $servant->servantDetails->updated_at->diffInMinutes(now()) <= 15,
            ];
        });

        return response()->json($data);
    }
}
