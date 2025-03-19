<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    public function allComplaintWorkers()
    {
        $user = auth()->user();

        $complaints = Complaint::with(['application'])->where('employe_id', $user->id)->paginate(10);

        try {
            if ($complaints->isEmpty()) {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Data semua pengaduan pekerja.',
                    'data' => 'Belum ada pengaduan.'
                ], 200);
            }

            $datas = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->roles->first()->name,
                    'access_token' => $user->access_token,
                ],
                'complaints' => [
                    'data' => $complaints->map(function ($query) {
                        return [
                            'id' => $query->id,
                            'employe_id' => $query->employe_id,
                            'message' => $query->message,
                            'status' => $query->status,
                            'notes_rejected' => $query->notes_rejected,
                            'file' => $query->file,
                            'servant_detail' => [
                                'name' => $query->application->servant->name,
                                'status' => $query->application->status,
                                'salary' => $query->application->salary,
                                'work_start_date' => $query->application->work_start_date,
                            ],
                        ];
                    }),
                    'pagination' => [
                        'current_page' => $complaints->currentPage(),
                        'per_page' => $complaints->perPage(),
                        'total' => $complaints->total(),
                        'last_page' => $complaints->lastPage(),
                        'current_page_url' => $complaints->url($complaints->currentPage()),
                        'next_page_url' => $complaints->nextPageUrl(),
                        'prev_page_url' => $complaints->previousPageUrl(),
                    ],
                ]
            ];

            return response()->json([
                'success' => 'success',
                'message' => 'Data semua pengaduan pekerja.',
                'data' => $datas
            ], 200);
        } catch (\Throwable $th) {
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }
}
