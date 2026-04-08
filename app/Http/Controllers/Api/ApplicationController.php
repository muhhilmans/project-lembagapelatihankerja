<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Vacancy;
use App\Models\Application;
use App\Models\RecomServant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function scheduleInterview()
    {
        $user = auth()->user();

        if ($user->hasRole('majikan')) {
            $schedules = Application::with(['vacancy', 'servant', 'employe'])
                ->whereHas('vacancy', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('employe_id', $user->id)
                ->where('status', 'interview')
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get();

        } elseif ($user->hasRole('pembantu')) {
            $schedules = Application::with(['vacancy', 'servant', 'employe'])
                ->where('servant_id', $user->id)
                ->where('status', 'interview')
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get();
        } else {
            return response()->json([
                'success' => 'failed',
                'message' => 'Role tidak valid.',
                'data' => null
            ], 403);
        }

        return response()->json([
            'success' => 'success',
            'message' => 'Data jadwal interview.',
            'data' => $schedules->isNotEmpty() ? $schedules : 'Belum ada jadwal interview.'
        ], 200);
    }

    public function allApplicant()
    {
        $user = auth()->user();

        $hires = Application::with(['servant', 'employe'])
            ->where('employe_id', $user->id)
            ->whereNotNull('employe_id')
            ->paginate(10);

        $indies = Application::with(['vacancy.user', 'servant'])
            ->whereHas('vacancy.user', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->whereNotNull('vacancy_id')
            ->paginate(10);

        try {
            if ($hires->isEmpty() && $indies->isEmpty()) {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Data semua pelamar.',
                    'data' => 'Belum ada pelamar.'
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
                'hires' => [
                    'data' => $hires->map(function ($hire) {
                        return $this->formatApplicationData($hire, 'employer_detail', $hire->employe);
                    }),
                    'pagination' => $this->formatPagination($hires),
                ],
                'indies' => [
                    'data' => $indies->map(function ($indie) {
                        return $this->formatApplicationData($indie, 'vacancy_detail', $indie->vacancy);
                    }),
                    'pagination' => $this->formatPagination($indies),
                ],
            ];

            return response()->json([
                'success' => 'success',
                'message' => 'Data semua pelamar.',
                'data' => $datas
            ], 200);
        } catch (\Throwable $th) {
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error'   => [
                    'message' => $th->getMessage()
                ]
            ], 500);
        }
    }

    public function applyJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vacancy_id' => ['required', 'exists:vacancies,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        $vacancyId = $request->vacancy_id;
        $servantId = auth()->id();

        // 1. Validasi: Cek apakah sudah melamar di lowongan ini
        $existingApplication = Application::where('servant_id', $servantId)
            ->where('vacancy_id', $vacancyId)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Anda sudah melamar untuk lowongan ini.',
                'data' => [
                    'status' => 'Sudah melamar',
                    'lowongan' => $existingApplication->vacancy->title,
                    'client' => $existingApplication->vacancy->user->name,
                    'applied_at' => $existingApplication->created_at
                ],
            ], 409);
        }

        // 2. Validasi: Cek apakah masih terikat kontrak aktif
        $hasActiveContract = Application::where('servant_id', $servantId)
            ->where('status', 'accepted')
            ->exists();

        if ($hasActiveContract) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Anda sedang terikat kontrak aktif dan tidak dapat melamar lowongan baru.',
            ], 403);
        }

        try {
            DB::beginTransaction();

            $application = Application::create([
                'servant_id' => $servantId,
                'vacancy_id' => $vacancyId,
            ]);

            DB::commit();

            return response()->json([
                'success' => 'success',
                'message' => 'Berhasil mengirimkan lamaran!',
                'data' => [
                    'id' => $application->id,
                    'lowongan' => $application->vacancy->title,
                    'client' => $application->vacancy->user->name,
                    'applied_at' => $application->created_at
                ]
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat mengirimkan lamaran.',
            ], 500);
        }
    }

    public function applyRecom(Request $request, Vacancy $vacancy, RecomServant $recomServant)
    {
        $validator = Validator::make($request->all(), [
            'notes' => ['nullable', 'string'],
            'interview_date' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $servant = $recomServant->servant;

        // Cek duplikasi persetujuan rekomendasi
        $existingApplication = Application::where('vacancy_id', $vacancy->id)
            ->where('servant_id', $servant->id)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'Pelamar sudah disetujui pada lowongan ini.',
            ], 409);
        }

        // Cek apakah pekerja terikat kontrak aktif (Perbaikan BUG $servantId -> $servant->id)
        $hasActiveContract = Application::where('servant_id', $servant->id)
            ->where('status', 'accepted')
            ->exists();

        if ($hasActiveContract) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Pekerja sedang terikat kontrak dan tidak dapat diproses untuk lowongan baru.',
            ], 409);
        }

        try {
            DB::beginTransaction();

            $application = Application::create([
                'vacancy_id' => $vacancy->id,
                'servant_id' => $servant->id,
                'status' => 'schedule',
                'notes_interview' => $data['notes'],
                'interview_date' => $data['interview_date'],
            ]);

            DB::commit();

            // SCRIPT NOTIFIKASI DIHAPUS SESUAI INSTRUKSI

            return response()->json([
                'success' => 'success',
                'message' => 'Berhasil menyetujui rekomendasi pelamar!',
                'data' => [
                    'id' => $application->id,
                    'vacancy_id' => $application->vacancy_id,
                    'status' => $application->status,
                    'notes_interview' => $application->notes_interview,
                    'interview_date' => $application->interview_date,
                ],
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat menyetujui rekomendasi.',
            ], 500);
        }
    }

    public function changeStatus(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'status'          => ['required', 'string'],
            'notes_interview' => ['nullable', 'string'],
            'notes_rejected'  => ['nullable', 'string'],
            'interview_date'  => ['nullable', 'date'],
            'salary'          => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        if (in_array($application->status, ['accepted', 'rejected', 'laidoff'])) {
            return response()->json([
                'status'  => 'failed',
                'message' => "Status pelamar sudah '{$application->status}' dan tidak dapat diubah lagi."
            ], 403);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            $updateData = ['status' => $data['status']];

            if ($data['status'] === 'schedule') {
                $updateData['notes_interview'] = $data['notes_interview'] ?? null;
                $updateData['interview_date']  = $data['interview_date'] ?? null;
            } elseif ($data['status'] === 'passed') {
                $updateData['salary'] = $data['salary'] ?? null;
            } elseif ($data['status'] === 'rejected') {
                $updateData['notes_rejected'] = $data['notes_rejected'] ?? null;
            }

            $application->fill($updateData);

            if (!$application->save()) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Perubahan status gagal disimpan. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data status berhasil dirubah!',
                'data'    => $application
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat mengubah status.',
            ], 500);
        }
    }

    public function allApplication()
    {
        $user = auth()->user();

        $hires = Application::with(['servant', 'employe'])
            ->where('servant_id', $user->id)
            ->whereNotNull('employe_id')
            ->paginate(10);

        $indies = Application::with(['servant', 'employe', 'vacancy'])
            ->where('servant_id', $user->id)
            ->whereNotNull('vacancy_id')
            ->paginate(10);

        try {
            if ($hires->isEmpty() && $indies->isEmpty()) {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Data semua lamaran.',
                    'data' => 'Belum ada lamaran.'
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
                'hires' => [
                    'data' => $hires->map(function ($hire) {
                        return $this->formatApplicationData($hire, 'employer_detail', $hire->employe);
                    }),
                    'pagination' => $this->formatPagination($hires),
                ],
                'indies' => [
                    'data' => $indies->map(function ($indie) {
                        return $this->formatApplicationData($indie, 'vacancy_detail', $indie->vacancy);
                    }),
                    'pagination' => $this->formatPagination($indies),
                ],
            ];

            return response()->json([
                'success' => 'success',
                'message' => 'Data semua pelamar.',
                'data' => $datas
            ], 200);
        } catch (\Throwable $th) {
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat mengambil data.',
            ], 500);
        }
    }

    public function chooseStatus(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'status'          => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        if (in_array($application->status, ['verify', 'contract', 'choose', 'accepted', 'rejected', 'laidoff'])) {
            return response()->json([
                'status'  => 'failed',
                'message' => "Status lamaran sudah '{$application->status}' dan tidak dapat diubah lagi."
            ], 403);
        }

        if (in_array($application->status, ['pending', 'schedule', 'interview'])) {
            return response()->json([
                'status'  => 'failed',
                'message' => "Status lamaran masih '{$application->status}' dan belum saatnya diubah."
            ], 403);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            $application->update(['status' => $data['status']]);
            DB::commit();

            // SCRIPT NOTIFIKASI DIHAPUS SESUAI INSTRUKSI

            return response()->json([
                'status'  => 'success',
                'message' => 'Data status berhasil dirubah!',
                'data'    => $application
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat mengubah status.',
            ], 500);
        }
    }

    // Helper method untuk meminimalisir kode berulang (DRY)
    private function formatApplicationData($app, $relationKey, $relationData)
    {
        $data = [
            'id' => $app->id,
            'servant_id' => $app->servant_id,
            'vacancy_id' => $app->vacancy_id,
            'employe_id' => $app->employe_id,
            'status' => $app->status,
            'salary_type' => $app->salary_type,
            'salary' => $app->salary,
            'is_infal' => $app->is_infal,
            'infal_frequency' => $app->infal_frequency,
            'admin_fee' => $app->admin_fee,
            'warranty_duration' => $app->warranty_duration,
            'end_reason' => $app->end_reason,
            'scheme_id' => $app->sheme_id, // Note: periksa apakah typo DB sheme_id atau scheme_id
            'garansi_id' => $app->garansi_id,
            'garansi_price' => $app->garansi_price,
            'infal_time_in' => $app->infal_time_in,
            'infal_time_out' => $app->infal_time_out,
            'infal_hourly_rate' => $app->infal_hourly_rate,
            'file_contract' => $app->file_contract,
            'work_start_date' => $app->work_start_date,
            'work_end_date' => $app->work_end_date,
            'servant_detail' => [
                'id' => $app->servant->id,
                'name' => $app->servant->name,
                'email' => $app->servant->email,
            ],
        ];

        if ($relationKey === 'employer_detail' && $relationData) {
            $data['employer_detail'] = [
                'id' => $relationData->id,
                'name' => $relationData->name,
                'address' => $relationData->employeDetails->address ?? '-',
                'phone' => $relationData->employeDetails->phone ?? '-',
            ];
        } elseif ($relationKey === 'vacancy_detail' && $relationData) {
             $data['vacancy_detail'] = [
                'id' => $relationData->id,
                'client' => $relationData->user->name ?? '-',
                'title' => $relationData->title,
                'status' => $relationData->status,
            ];
        }

        return $data;
    }

    private function formatPagination($paginator)
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'current_page_url' => $paginator->url($paginator->currentPage()),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
        ];
    }
}
