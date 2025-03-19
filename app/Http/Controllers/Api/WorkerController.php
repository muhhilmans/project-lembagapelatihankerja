<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WorkerController extends Controller
{
    public function allWorker()
    {
        $user = auth()->user();
        $workers = Application::with(['servant', 'employe', 'vacancy'])
            ->where('employe_id', $user->id)
            ->where('status', 'accepted')
            ->whereNotNull('employe_id')
            ->paginate(10);

        try {
            if ($workers->isEmpty()) {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Data semua pekerja.',
                    'data' => 'Belum ada pekerja.'
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
                'worker' => [
                    'data' => $workers->map(function ($query) {
                        return [
                            'id' => $query->id,
                            'servant_id' => $query->servant_id,
                            'vacancy_id' => $query->vacancy_id,
                            'employe_id' => $query->employe_id,
                            'status' => $query->status,
                            'interview_date' => $query->interview_date,
                            'link_interview' => $query->link_interview,
                            'notes_interview' => $query->notes_interview,
                            'notes_verify' => $query->notes_verify,
                            'notes_accepted' => $query->notes_accepted,
                            'notes_rejected' => $query->notes_rejected,
                            'salary' => $query->salary,
                            'file_contract' => $query->file_contract,
                            'work_start_date' => $query->work_start_date,
                            'work_end_date' => $query->work_end_date,
                            'servant_detail' => [
                                'id' => $query->servant->id,
                                'name' => $query->servant->name,
                                'username' => $query->servant->username,
                                'email' => $query->servant->email,
                                'gender'           => $query->servant->servantDetails->gender ?? 'not_filled',
                                'place_of_birth'   => $query->servant->servantDetails->place_of_birth ?? '-',
                                'date_of_birth'    => $query->servant->servantDetails->date_of_birth,
                                'religion'         => $query->servant->servantDetails->religion ?? '-',
                                'marital_status'   => $query->servant->servantDetails->marital_status ?? 'not_filled',
                                'children'         => $query->servant->servantDetails->children ?? 0,
                                'last_education'   => $query->servant->servantDetails->last_education ?? 'not_filled',
                                'phone'            => $query->servant->servantDetails->phone ?? '-',
                                'emergency_number' => $query->servant->servantDetails->emergency_number ?? '-',
                                'address'          => $query->servant->servantDetails->address ?? '-',
                                'rt'               => $query->servant->servantDetails->rt,
                                'rw'               => $query->servant->servantDetails->rw,
                                'village'          => $query->servant->servantDetails->village,
                                'district'         => $query->servant->servantDetails->district,
                                'regency'          => $query->servant->servantDetails->regency,
                                'province'         => $query->servant->servantDetails->province,
                                'is_bank'          => $query->servant->servantDetails->is_bank ?? 0,
                                'bank_name'        => $query->servant->servantDetails->bank_name ?? '-',
                                'account_number'   => $query->servant->servantDetails->account_number ?? '-',
                                'is_bpjs'          => $query->servant->servantDetails->is_bpjs ?? 0,
                                'type_bpjs'        => $query->servant->servantDetails->type_bpjs ?? 'Ketenagakerjaan',
                                'number_bpjs'      => $query->servant->servantDetails->number_bpjs ?? '-',
                                'photo'            => $query->servant->servantDetails->photo,
                                'identity_card'    => $query->servant->servantDetails->identity_card,
                                'family_card'      => $query->servant->servantDetails->family_card,
                                'working_status'   => $query->servant->servantDetails->working_status ?? 0,
                                'experience'       => $query->servant->servantDetails->experience ?? '-',
                                'description'      => $query->servant->servantDetails->description ?? '-',
                                'is_inval'         => $query->servant->servantDetails->is_inval ?? 0,
                                'is_stay'          => $query->servant->servantDetails->is_stay ?? 0,
                                'profession'       => $query->servant->servantDetails->profession->name ?? null,
                                'skills' => $query->servant->servantSkills->map(function ($skill) {
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
                        'current_page' => $workers->currentPage(),
                        'per_page' => $workers->perPage(),
                        'total' => $workers->total(),
                        'last_page' => $workers->lastPage(),
                        'current_page_url' => $workers->url($workers->currentPage()),
                        'next_page_url' => $workers->nextPageUrl(),
                        'prev_page_url' => $workers->previousPageUrl(),
                    ],
                ],
            ];
    
            return response()->json([
                'success' => 'success',
                'message' => 'Data semua pekerja.',
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

    public function rejectWorker(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string'],
            'work_end_date' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        $datas = $validator->validated();

        try {
            DB::beginTransaction();

            $update = $application->update([
                'status' => $datas['status'],
                'work_end_date' => $datas['work_end_date'],
            ]);

            if (!$update) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Pemberhentian pekerja gagal disimpan. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Pekerja berhasil diberhentikan!',
                'data'    => $application
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat memberhentikan pekerja.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }

    public function complaintWorker(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            $user = auth()->user();

            $complaint = Complaint::where('application_id', $application->id)->where('employe_id', $user->id)->first();

            if ($complaint) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Pengaduan pekerja sudah dikirimkan. Silakan coba lagi.',
                    'data'    => $complaint
                ], 409);
            }

            $store = Complaint::create([
                'application_id' => $application->id,
                'servant_id' => null,
                'employe_id' => $user->id,
                'message' => $data['message'],
                'status' => 'pending',
            ]);

            if (!$store) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Pengaduan pekerja gagal disimpan. Silakan coba lagi.'
                ], 502);
            }
            
            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Pengaduan pekerja berhasil dikirimkan!',
                'data'    => $store
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat mengirimkan aduan.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }
}
