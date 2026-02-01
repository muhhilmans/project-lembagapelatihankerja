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
    public function store(Request $request, $applicationId)
    {
        $user = auth()->user();

        $application = Application::find($applicationId);

        if (!$application) {
            return response()->json(['message' => 'Data pekerjaan tidak ditemukan'], 404);
        }

        // 1. Tentukan Siapa Majikan & Siapa Pembantu di kontrak ini
        $employerId = $application->employe_id ?? ($application->vacancy ? $application->vacancy->user_id : null);
        $servantId  = $application->servant_id;

        // 2. Deteksi Peran User yang Login & Tentukan Target Review
        $revieweeId = null; // Siapa yang akan dinilai

        if ($user->id == $employerId) {
            // Kalau yang login Majikan, targetnya Pembantu
            $revieweeId = $servantId;
        } elseif ($user->id == $servantId) {
            // Kalau yang login Pembantu, targetnya Majikan
            $revieweeId = $employerId;
        } else {
            // Kalau user tidak terlibat dalam kontrak ini (Orang asing)
            return response()->json(['message' => 'Anda tidak memiliki akses ke kontrak ini'], 403);
        }

        // 3. Validasi Status (Hanya boleh jika sudah selesai/stop)
        $allowedStatuses = ['rejected', 'finished', 'stopped', 'laidoff'];
        if (!in_array($application->status, $allowedStatuses)) {
            return response()->json([
                'message' => 'Review hanya bisa diberikan setelah kontrak berakhir.'
            ], 400);
        }

        // 4. Validasi Input
        $validator = Validator::make($request->all(), [
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 5. Cek Double Review (Satu orang cuma boleh review 1x per kontrak)
        $existingReview = Review::where('application_id', $application->id)
                                ->where('reviewer_id', $user->id)
                                ->first();

        if ($existingReview) {
            return response()->json(['message' => 'Anda sudah memberikan review untuk kontrak ini'], 409);
        }

        try {
            DB::beginTransaction();

            $review = Review::create([
                'application_id' => $application->id,
                'reviewer_id'    => $user->id,       // Pelaku (Bisa Majikan/Pembantu)
                'reviewee_id'    => $revieweeId,     // Target (Bisa Pembantu/Majikan)
                'rating'         => $request->rating,
                'comment'        => $request->comment,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Review berhasil dikirim!',
                'data'    => $review
            ], 201);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
