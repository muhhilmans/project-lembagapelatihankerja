<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Vacancy;
use App\Models\Application;
use App\Models\RecomServant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
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
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat mengirimkan lamaran.',
                'error' => $th->getMessage()
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

        $existingApplication = Application::where('vacancy_id', $vacancy->id)
            ->where('servant_id', $servant->id)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'Pelamar sudah disetujui pada lowongan ini.',
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
            
            return response()->json([
                'success' => 'success',
                'message' => 'Berhasil menyetujui rekomendasi pelamar!',
                'data' => [
                    'id' => $application->id,
                    'vacancy_id' => $application->vacancy_id,
                    'servant' => [
                        'id' => $servant->id,
                        'name' => $servant->name,
                        'email' => $servant->email,
                        'detail' => collect($servant->servantDetails)->except([
                            'id',
                            'servant_id',
                            'created_at',
                            'updated_at'
                        ]),
                    ],
                    'status' => $application->status,
                    'notes_interview' => $application->notes_interview,
                    'interview_date' => $application->interview_date,
                ],
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat menyetujui rekomendasi.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
