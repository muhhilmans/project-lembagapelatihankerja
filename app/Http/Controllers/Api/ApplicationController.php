<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Vacancy;
use App\Models\Application;
use App\Models\RecomServant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
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
                    return [
                        'id' => $hire->id,
                        'servant_id' => $hire->servant_id,
                        'employe_id' => $hire->employe_id,
                        'status' => $hire->status,
                        'interview_date' => $hire->interview_date,
                        'link_interview' => $hire->link_interview,
                        'notes_interview' => $hire->notes_interview,
                        'notes_verify' => $hire->notes_verify,
                        'notes_accepted' => $hire->notes_accepted,
                        'notes_rejected' => $hire->notes_rejected,
                        'salary' => $hire->salary,
                        'file_contract' => $hire->file_contract,
                        'work_start_date' => $hire->work_start_date,
                        'work_end_date' => $hire->work_end_date,
                        'servant_detail' => [
                            'id' => $hire->servant->id,
                            'name' => $hire->servant->name,
                            'username' => $hire->servant->username,
                            'email' => $hire->servant->email,
                            'gender'           => $hire->servant->servantDetails->gender ?? 'not_filled',
                            'place_of_birth'   => $hire->servant->servantDetails->place_of_birth ?? '-',
                            'date_of_birth'    => $hire->servant->servantDetails->date_of_birth,
                            'religion'         => $hire->servant->servantDetails->religion ?? '-',
                            'marital_status'   => $hire->servant->servantDetails->marital_status ?? 'not_filled',
                            'children'         => $hire->servant->servantDetails->children ?? 0,
                            'last_education'   => $hire->servant->servantDetails->last_education ?? 'not_filled',
                            'phone'            => $hire->servant->servantDetails->phone ?? '-',
                            'emergency_number' => $hire->servant->servantDetails->emergency_number ?? '-',
                            'address'          => $hire->servant->servantDetails->address ?? '-',
                            'rt'               => $hire->servant->servantDetails->rt,
                            'rw'               => $hire->servant->servantDetails->rw,
                            'village'          => $hire->servant->servantDetails->village,
                            'district'         => $hire->servant->servantDetails->district,
                            'regency'          => $hire->servant->servantDetails->regency,
                            'province'         => $hire->servant->servantDetails->province,
                            'is_bank'          => $hire->servant->servantDetails->is_bank ?? 0,
                            'bank_name'        => $hire->servant->servantDetails->bank_name ?? '-',
                            'account_number'   => $hire->servant->servantDetails->account_number ?? '-',
                            'is_bpjs'          => $hire->servant->servantDetails->is_bpjs ?? 0,
                            'type_bpjs'        => $hire->servant->servantDetails->type_bpjs ?? 'Ketenagakerjaan',
                            'number_bpjs'      => $hire->servant->servantDetails->number_bpjs ?? '-',
                            'photo'            => $hire->servant->servantDetails->photo,
                            'identity_card'    => $hire->servant->servantDetails->identity_card,
                            'family_card'      => $hire->servant->servantDetails->family_card,
                            'working_status'   => $hire->servant->servantDetails->working_status ?? 0,
                            'experience'       => $hire->servant->servantDetails->experience ?? '-',
                            'description'      => $hire->servant->servantDetails->description ?? '-',
                            'is_inval'         => $hire->servant->servantDetails->is_inval ?? 0,
                            'is_stay'          => $hire->servant->servantDetails->is_stay ?? 0,
                            'profession'       => $hire->servant->servantDetails->profession->name ?? null,
                            'skills' => $hire->servant->servantSkills->map(function ($skill) {
                                return [
                                    'id' => $skill->id,
                                    'user_id' => $skill->user_id,
                                    'skill' => $skill->skill,
                                    'keahlian' => $skill->level
                                ];
                            }),
                        ],
                    ];
                }),
                'pagination' => [
                    'current_page' => $hires->currentPage(),
                    'per_page' => $hires->perPage(),
                    'total' => $hires->total(),
                    'last_page' => $hires->lastPage(),
                    'next_page_url' => $hires->nextPageUrl(),
                    'prev_page_url' => $hires->previousPageUrl(),
                ],
            ],
            'indies' => [
                'data' => $indies->map(function ($indie) {
                    return [
                        'id' => $indie->id,
                        'servant_id' => $indie->servant_id,
                        'vacancy_id' => $indie->vacancy_id,
                        'status' => $indie->status,
                        'interview_date' => $indie->interview_date,
                        'link_interview' => $indie->link_interview,
                        'notes_interview' => $indie->notes_interview,
                        'notes_verify' => $indie->notes_verify,
                        'notes_accepted' => $indie->notes_accepted,
                        'notes_rejected' => $indie->notes_rejected,
                        'salary' => $indie->salary,
                        'file_contract' => $indie->file_contract,
                        'work_start_date' => $indie->work_start_date,
                        'work_end_date' => $indie->work_end_date,
                        'servant_detail' => [
                            'id' => $indie->servant->id,
                            'name' => $indie->servant->name,
                            'username' => $indie->servant->username,
                            'email' => $indie->servant->email,
                            'gender'           => $indie->servant->servantDetails->gender ?? 'not_filled',
                            'place_of_birth'   => $indie->servant->servantDetails->place_of_birth ?? '-',
                            'date_of_birth'    => $indie->servant->servantDetails->date_of_birth,
                            'religion'         => $indie->servant->servantDetails->religion ?? '-',
                            'marital_status'   => $indie->servant->servantDetails->marital_status ?? 'not_filled',
                            'children'         => $indie->servant->servantDetails->children ?? 0,
                            'last_education'   => $indie->servant->servantDetails->last_education ?? 'not_filled',
                            'phone'            => $indie->servant->servantDetails->phone ?? '-',
                            'emergency_number' => $indie->servant->servantDetails->emergency_number ?? '-',
                            'address'          => $indie->servant->servantDetails->address ?? '-',
                            'rt'               => $indie->servant->servantDetails->rt,
                            'rw'               => $indie->servant->servantDetails->rw,
                            'village'          => $indie->servant->servantDetails->village,
                            'district'         => $indie->servant->servantDetails->district,
                            'regency'          => $indie->servant->servantDetails->regency,
                            'province'         => $indie->servant->servantDetails->province,
                            'is_bank'          => $indie->servant->servantDetails->is_bank ?? 0,
                            'bank_name'        => $indie->servant->servantDetails->bank_name ?? '-',
                            'account_number'   => $indie->servant->servantDetails->account_number ?? '-',
                            'is_bpjs'          => $indie->servant->servantDetails->is_bpjs ?? 0,
                            'type_bpjs'        => $indie->servant->servantDetails->type_bpjs ?? 'Ketenagakerjaan',
                            'number_bpjs'      => $indie->servant->servantDetails->number_bpjs ?? '-',
                            'photo'            => $indie->servant->servantDetails->photo,
                            'identity_card'    => $indie->servant->servantDetails->identity_card,
                            'family_card'      => $indie->servant->servantDetails->family_card,
                            'working_status'   => $indie->servant->servantDetails->working_status ?? 0,
                            'experience'       => $indie->servant->servantDetails->experience ?? '-',
                            'description'      => $indie->servant->servantDetails->description ?? '-',
                            'is_inval'         => $indie->servant->servantDetails->is_inval ?? 0,
                            'is_stay'          => $indie->servant->servantDetails->is_stay ?? 0,
                            'profession'       => $indie->servant->servantDetails->profession->name ?? null,
                            'skills' => $indie->servant->servantSkills->map(function ($skill) {
                                return [
                                    'id' => $skill->id,
                                    'user_id' => $skill->user_id,
                                    'skill' => $skill->skill,
                                    'keahlian' => $skill->level
                                ];
                            }),
                        ],
                        'vacancy_detail' => [
                            'id' => $indie->vacancy->id,
                            'client' => $indie->vacancy->user->name,
                            'title' => $indie->vacancy->title,
                            'profession' => $indie->vacancy->profession->name,
                            'description' => $indie->vacancy->description,
                            'requirements' => $indie->vacancy->requirements,
                            'benefits' => $indie->vacancy->benefits,
                            'closing_date' => $indie->vacancy->closing_date,
                            'limit' => $indie->vacancy->limit,
                            'status' => $indie->vacancy->status,
                        ],
                    ];
                }),
                'pagination' => [
                    'current_page' => $indies->currentPage(),
                    'per_page' => $indies->perPage(),
                    'total' => $indies->total(),
                    'last_page' => $indies->lastPage(),
                    'next_page_url' => $indies->nextPageUrl(),
                    'prev_page_url' => $indies->previousPageUrl(),
                ],
            ],
        ];

        return response()->json([
            'success' => 'success',
            'message' => 'Data semua pelamar.',
            'data' => $datas
        ], 200);
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
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat mengirimkan lamaran.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
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
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat menyetujui rekomendasi.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }
}
