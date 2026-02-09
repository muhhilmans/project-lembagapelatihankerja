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
}
