<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class ComplaintController
 * Menangani pengaduan dari majikan dan pembantu.
 */
class ComplaintController extends Controller
{
    /**
     * Mengambil semua pengaduan terkait pekerja (Tampilan Majikan).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allComplaintWorkers()
    {
        $user = auth()->user();

        // Employer View: get complaints involving me
        $complaints = Pengaduan::with(['application'])
            ->where('reporter_id', $user->id)
            ->orWhere('reported_user_id', $user->id)
            ->paginate(10);

        try {
            if ($complaints->isEmpty()) {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Data semua pengaduan pekerja.',
                    'data' => 'Belum ada pengaduan.',
                    'raw_data' => [] // Fix frontend check if it expects array
                ], 200);
            }

            $datas = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->roles->first()->name ?? '',
                    'access_token' => $user->access_token ?? '',
                ],
                'complaints' => [
                    'data' => $complaints->map(function ($query) {
                        return [
                            'id' => $query->id,
                            'employe_id' => $query->reporter_id, // Best guess mapping
                            'message' => $query->description,
                            'status' => $query->status,
                            'notes_rejected' => null, // Field removed
                            'file' => null, // Field removed
                            'servant_detail' => [
                                'name' => $query->application && $query->application->servant ? $query->application->servant->name : 'N/A',
                                'status' => $query->application ? $query->application->status : '-',
                                'salary' => $query->application ? $query->application->salary : 0,
                                'work_start_date' => $query->application ? $query->application->work_start_date : null,
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

    /**
     * Mengambil semua pengaduan terkait pekerjaan (Tampilan Pembantu).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allComplaintWork()
    {
        $user = auth()->user();

        // Servant View: get complaints involving me
        $complaints = Pengaduan::with(['application'])
             ->where('reporter_id', $user->id)
            ->orWhere('reported_user_id', $user->id)
            ->paginate(10);

        try {
            if ($complaints->isEmpty()) {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Data semua pengaduan pekerjaan.',
                    'data' => 'Belum ada pengaduan.',
                    'raw_data' => []
                ], 200);
            }

            $datas = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->roles->first()->name ?? '',
                    'access_token' => $user->access_token ?? '',
                ],
                'complaints' => [
                    'data' => $complaints->map(function ($query) {
                         // Determine 'other' Name (Employer)
                         // logic: if I am reporter, who is reported?
                         // But use application->employe for consistency with old code
                         $employerName = 'N/A';
                         if ($query->application) {
                             if ($query->application->employe) $employerName = $query->application->employe->name;
                             elseif ($query->application->vacancy && $query->application->vacancy->user) $employerName = $query->application->vacancy->user->name;
                         }

                        return [
                            'id' => $query->id,
                            'message' => $query->description,
                            'status' => $query->status,
                            'notes_rejected' => null,
                            'file' => null,
                            'work_detail' => [
                                'name' => $employerName,
                                'status' => $query->application ? $query->application->status : '-',
                                'salary' => $query->application ? $query->application->salary : 0,
                                'work_start_date' => $query->application ? $query->application->work_start_date : null,
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
                'message' => 'Data semua pengaduan pekerjaan.',
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
