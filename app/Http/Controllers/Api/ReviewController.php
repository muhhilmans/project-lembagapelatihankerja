<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request, Application $application)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $user = auth()->user();

        // 2. Validasi Status Kontrak — harus 'laidoff'
        if ($application->status !== 'laidoff') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Review hanya dapat diberikan setelah kontrak selesai.',
            ], 403);
        }

        // 3. Hak Akses — user harus salah satu pihak dalam application
        if ($user->id !== $application->servant_id && $user->id !== $application->employe_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki akses untuk mereview kontrak ini.',
            ], 403);
        }

        // 4. Cegah Review Duplikat
        $existingReview = Review::where('application_id', $application->id)
                                ->where('reviewer_id', $user->id)
                                ->first();

        if ($existingReview) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda sudah memberikan ulasan untuk kontrak ini.',
            ], 422);
        }

        // 5. Tentukan reviewee_id berdasarkan role Spatie
        $roleName = $user->roles->first()->name ?? null;

        if ($roleName === 'majikan') {
            $revieweeId = $application->servant_id;
        } elseif ($roleName === 'pembantu') {
            $revieweeId = $application->employe_id
                ?? optional($application->vacancy)->user_id;
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki akses untuk mereview kontrak ini.',
            ], 403);
        }

        // 6. Simpan Review dalam transaction
        try {
            $review = DB::transaction(function () use ($application, $user, $revieweeId, $validated) {
                return Review::create([
                    'application_id' => $application->id,
                    'reviewer_id'    => $user->id,
                    'reviewee_id'    => $revieweeId,
                    'rating'         => $validated['rating'],
                    'comment'        => $validated['comment'],
                ]);
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Review berhasil dikirim!',
                'data'    => $review,
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $th->getMessage(),
            ], 500);
        }
    }
}
