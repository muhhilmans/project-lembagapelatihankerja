<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Voucher;
use App\Models\Complaint;
use App\Models\Application;
use App\Traits\ApiResponse;
use App\Models\WorkerSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WorkerController extends Controller
{
    use ApiResponse;
    
    public function allWorker(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->input('search');

            $workers = Application::with(['servant', 'employe', 'vacancy'])
                ->where(function ($query) use ($user) {
                    $query->where('employe_id', $user->id)
                        ->orWhereHas('vacancy', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        });
                })
                ->where('status', 'accepted')
                ->when($search, function ($q) use ($search) {
                    $q->whereHas('servant', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->paginate(10);

        
            if ($workers->isEmpty()) {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Data semua pekerja.',
                    'data' =>  [
                        'user' => [
                                'id' => $user->id,
                                'name' => $user->name,
                                'username' => $user->username,
                                'email' => $user->email,
                                'role' => $user->roles->first()->name,
                                'access_token' => $user->access_token,
                            ],
                        'worker' => 'Belum ada pekerja.'
                        ]
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

    public function showWorker($id)
    {
        $user = auth()->user();
        $worker = Application::with(['servant', 'employe', 'vacancy'])
            ->where(function ($query) use ($user) {
                $query->where('employe_id', $user->id)
                    ->orWhereHas('vacancy', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            ->where('status', 'accepted')
            ->find($id);

        $workerSalaries = WorkerSalary::with(['voucher'])->where('application_id', $id)->get();

        if (!$worker) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data pekerja tidak ditemukan.',
            ], 404);
        }

        try {
            $datas = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->roles->first()->name,
                    'access_token' => $user->access_token,
                ],
                'detail' => [
                    'id' => $worker->id,
                    'servant_id' => $worker->servant_id,
                    'client' => $worker->employe_id ? $worker->employe->name : $worker->vacancy->user->name,
                    'status' => $worker->status,
                    'salary' => $worker->salary,
                    'work_start_date' => $worker->work_start_date,
                    'servant_detail' => [
                        'id' => $worker->servant->id,
                        'name' => $worker->servant->name,
                        'username' => $worker->servant->username,
                        'email' => $worker->servant->email,
                        'gender'           => $worker->servant->servantDetails->gender ?? 'not_filled',
                        'place_of_birth'   => $worker->servant->servantDetails->place_of_birth ?? '-',
                        'date_of_birth'    => $worker->servant->servantDetails->date_of_birth,
                        'religion'         => $worker->servant->servantDetails->religion ?? '-',
                        'marital_status'   => $worker->servant->servantDetails->marital_status ?? 'not_filled',
                        'children'         => $worker->servant->servantDetails->children ?? 0,
                        'last_education'   => $worker->servant->servantDetails->last_education ?? 'not_filled',
                        'phone'            => $worker->servant->servantDetails->phone ?? '-',
                        'emergency_number' => $worker->servant->servantDetails->emergency_number ?? '-',
                        'address'          => $worker->servant->servantDetails->address ?? '-',
                        'rt'               => $worker->servant->servantDetails->rt,
                        'rw'               => $worker->servant->servantDetails->rw,
                        'village'          => $worker->servant->servantDetails->village,
                        'district'         => $worker->servant->servantDetails->district,
                        'regency'          => $worker->servant->servantDetails->regency,
                        'province'         => $worker->servant->servantDetails->province,
                        'is_bank'          => $worker->servant->servantDetails->is_bank ?? 0,
                        'bank_name'        => $worker->servant->servantDetails->bank_name ?? '-',
                        'account_number'   => $worker->servant->servantDetails->account_number ?? '-',
                        'is_bpjs'          => $worker->servant->servantDetails->is_bpjs ?? 0,
                        'type_bpjs'        => $worker->servant->servantDetails->type_bpjs ?? 'Ketenagakerjaan',
                        'number_bpjs'      => $worker->servant->servantDetails->number_bpjs ?? '-',
                        'photo'            => $worker->servant->servantDetails->photo,
                        'identity_card'    => $worker->servant->servantDetails->identity_card,
                        'family_card'      => $worker->servant->servantDetails->family_card,
                        'working_status'   => $worker->servant->servantDetails->working_status ?? 0,
                        'experience'       => $worker->servant->servantDetails->experience ?? '-',
                        'description'      => $worker->servant->servantDetails->description ?? '-',
                        'is_inval'         => $worker->servant->servantDetails->is_inval ?? 0,
                        'is_stay'          => $worker->servant->servantDetails->is_stay ?? 0,
                        'profession'       => $worker->servant->servantDetails->profession->name ?? null,
                        'professions'       => $worker->servant->servantDetails->professions->map(function ($p) {
                                                return [
                                                    'id' => $p->id,
                                                    'name' => $p->name,
                                                    'file_draft' => $p->file_draft
                                                ];
                                            }),
                        'skills' => $worker->servant->servantSkills->map(function ($skill) {
                            return [
                                'id' => $skill->id,
                                'user_id' => $skill->user_id,
                                'skill' => $skill->skill,
                                'keahlian' => $skill->level
                            ];
                        }),
                    ],
                ],
                'gaji' => $workerSalaries->makeHidden(['total_salary_pembantu', 'voucher_id', 'created_at', 'updated_at']),
            ];

            return response()->json([
                'success' => 'success',
                'message' => 'Data detail pekerja.',
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

    public function presenceWorker(Request $request, Application $application)
    {
        if (!$application) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data pekerjaan tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required',
            'presence' => 'required|integer|min:0',
            'voucher' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $schemaSalary = optional($application->schemaSalary);
        $bpjsClient = $schemaSalary->bpjs_client ? 20000 : 0;
        $bpjsMitra = $schemaSalary->bpjs_mitra ? 20000 : 0;

        $voucher = null;
        if (!empty($data['voucher'])) {
            $voucher = Voucher::where('code', $data['voucher'])->first();

            if (!$voucher) {
                return response()->json([
                    'success' => 'failed',
                    'message' => 'Kode voucher tidak ditemukan',
                ], 400);
            }

            if (!$voucher->is_active) {
                return response()->json([
                    'success' => 'failed',
                    'message' => 'Kode voucher sudah tidak aktif',
                ], 400);
            }

            if ($voucher->expired_date && $voucher->expired_date < Carbon::now()->format('Y-m-d')) {
                return response()->json([
                    'success' => 'failed',
                    'message' => 'Kode voucher sudah tidak berlaku',
                ], 400);
            }

            $usedCount = WorkerSalary::where('voucher_id', $voucher->id)->count();
            $usedInApplication = $application->workerSalary()->where('voucher_id', $voucher->id)->count();

            if ($voucher->people_used && $usedCount >= $voucher->people_used) {
                if (!($voucher->time_used && $usedInApplication < $voucher->time_used)) {
                    return response()->json([
                        'success' => 'failed',
                        'message' => 'Kode voucher telah mencapai batas penggunaan',
                    ], 400);
                }
            }

            if ($voucher->time_used && $usedInApplication >= $voucher->time_used) {
                return response()->json([
                    'success' => 'failed',
                    'message' => 'Kode voucher telah mencapai batas penggunaan pada pekerja ini',
                ], 400);
            }
        }

        $month = Carbon::createFromFormat('Y-m', $data['month']);
        $daysInMonth = $month->daysInMonth;
        $daySalary = $application->salary / $daysInMonth;

        $totalSalary = $data['presence'] * $daySalary;
        $discount = $voucher ? ($application->schemaSalary->adds_client - ($voucher->discount / 100)) : $application->schemaSalary->adds_client;

        $majikanBonus = $totalSalary * $discount;
        $totalSalaryMajikan = ($totalSalary + $majikanBonus) + $bpjsClient;

        $addSalaryPembantu = $totalSalary * $application->schemaSalary->adds_mitra;
        $totalSalaryPembantu = ($totalSalary - $addSalaryPembantu) - $bpjsMitra;

        $dataSalary = [
            'day_salary' => ceil($daySalary),
            'total_salary' => ceil($totalSalary),
            'total_salary_majikan' => ceil($totalSalaryMajikan),
            'total_salary_pembantu' => ceil($totalSalaryPembantu),
        ];

        try {
            DB::beginTransaction();

            $store = WorkerSalary::create([
                'application_id' => $application->id,
                'month' => Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth()->format('Y-m-d'),
                'presence' => $data['presence'],
                'total_salary' => $dataSalary['total_salary'],
                'total_salary_majikan' => $dataSalary['total_salary_majikan'],
                'total_salary_pembantu' => $dataSalary['total_salary_pembantu'],
                'voucher_id' => $voucher ? $voucher->id : null,
            ]);

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Absensi pekerja berhasil dikirimkan!',
                'data'    => $store
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat mengirimkan absensi.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }

    public function updatePresenceWorker(Request $request, Application $application, WorkerSalary $salary)
    {
        if (!$salary) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data gaji pekerja tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required',
            'presence' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $schemaSalary = optional($application->schemaSalary);
        $bpjsClient = $schemaSalary->bpjs_client ? 20000 : 0;
        $bpjsMitra = $schemaSalary->bpjs_mitra ? 20000 : 0;

        $voucher = null;
        if (!empty($data['voucher'])) {
            $voucher = Voucher::where('code', $data['voucher'])->first();

            if (!$voucher) {
                return response()->json([
                    'success' => 'failed',
                    'message' => 'Kode voucher tidak ditemukan',
                ], 400);
            }

            if (!$voucher->is_active || ($voucher->expired_date && $voucher->expired_date < now()->format('Y-m-d'))) {
                return response()->json([
                    'success' => 'failed',
                    'message' => 'Kode voucher tidak aktif atau sudah kadaluarsa',
                ], 400);
            }

            $usedCount = WorkerSalary::where('voucher_id', $voucher->id)->count();
            $usedInApplication = $application->workerSalary()->where('voucher_id', $voucher->id)->count();

            if ($voucher->people_used && $usedCount >= $voucher->people_used) {
                if (!($voucher->time_used && $usedInApplication < $voucher->time_used)) {
                    return response()->json([
                        'success' => 'failed',
                        'message' => 'Kode voucher telah mencapai batas penggunaan',
                    ], 400);
                }
            }

            if ($voucher->time_used && $usedInApplication >= $voucher->time_used) {
                return response()->json([
                    'success' => 'failed',
                    'message' => 'Kode voucher telah mencapai batas penggunaan pada pekerja ini',
                ], 400);
            }
        }

        $month = Carbon::createFromFormat('Y-m', $data['month']);
        $daysInMonth = $month->daysInMonth;
        $daySalary = $application->salary / $daysInMonth;

        $totalSalary = $data['presence'] * $daySalary;
        $discount = $voucher ? ($schemaSalary->adds_client - ($voucher->discount / 100)) : $schemaSalary->adds_client;

        $majikanBonus = $totalSalary * $discount;
        $totalSalaryMajikan = ($totalSalary + $majikanBonus) + $bpjsClient;

        $addSalaryPembantu = $totalSalary * $schemaSalary->adds_mitra;
        $totalSalaryPembantu = ($totalSalary - $addSalaryPembantu) - $bpjsMitra;

        $dataSalary = [
            'day_salary' => ceil($daySalary),
            'total_salary' => ceil($totalSalary),
            'total_salary_majikan' => ceil($totalSalaryMajikan),
            'total_salary_pembantu' => ceil($totalSalaryPembantu),
        ];

        try {
            DB::beginTransaction();

            $update = $salary->update([
                'month' => $month->startOfMonth()->format('Y-m-d'),
                'presence' => $data['presence'],
                'total_salary' => $dataSalary['total_salary'],
                'total_salary_majikan' => $dataSalary['total_salary_majikan'],
                'total_salary_pembantu' => $dataSalary['total_salary_pembantu'],
                'voucher_id' => $voucher ? $voucher->id : $salary->voucher_id,
            ]);

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Absensi pekerja berhasil diperbarui!',
                'data'    => $update
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat memperbarui absensi.',
                'error'   => [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine()
                ]
            ], 500);
        }
    }

    public function uploadMajikan(Request $request, Application $app)
    {
        $validator = Validator::make($request->all(), [
            'proof_majikan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'worker_salary_id' => 'required|exists:worker_salaries,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->all()[0]);
        }

        $data = $validator->validated();

        try {
            $salary = WorkerSalary::find($request->worker_salary_id);

            if(!$salary) {
                return $this->errorResponse('salary tidak ditemukan');
            }

            $majikanName = str_replace(' ', '_', ($app->vacancy ? $app->vacancy->user->name : $app->employe->name));
            $servantName = str_replace(' ', '_', $app->servant->name);
            $date = Carbon::parse($salary->month)->format('M-Y');
            $directory = "payments/{$majikanName}/{$servantName}";
            $fileName = "proof_majikan_" . $date . "_{$servantName}." . $request->file('proof_majikan')->getClientOriginalExtension();
            $storagePath = "public/{$directory}";

            if (!Storage::exists($storagePath)) {
                Storage::makeDirectory($storagePath);
            }

            if ($salary->payment_majikan_image && Storage::exists("payments/{$salary->payment_majikan_image}")) {
                Storage::delete("payments/{$salary->payment_majikan_image}");
            }

            $path = $request->file('proof_majikan')->storeAs($storagePath, $fileName);

            DB::transaction(function () use ($salary, $path) {
                $salary->update([
                    'payment_majikan_image' => str_replace('public/payments/', '', $path),
                ]);
            });

            // Alert::success('Berhasil', 'Berhasil mengupload bukti pembayaran!');
            return $this->successResponse($salary, 'Berhasil mengupload bukti pembayaran');
        } catch (\Throwable $th) {
            return $this->errorResponse('kesalahan sistem', $th->getMessage());
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

    public function allWork(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');

        $workers = Application::with(['servant', 'employe', 'vacancy'])
            ->where('servant_id', $user->id)
            ->where('status', 'accepted')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('employe', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vacancy.user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->paginate(10);

        try {
            if ($workers->isEmpty()) {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Data semua pekerjaan.',
                    'data' => 'Belum ada pekerjaan.'
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
                            'client' => $query->employe ? $query->employe->name : $query->vacancy->user->name,
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
                'message' => 'Data semua pekerjaan.',
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

    public function showWork($id)
    {
        $user = auth()->user();
        $worker = Application::with(['servant', 'employe', 'vacancy'])
            ->where('servant_id', $user->id)
            ->where('status', 'accepted')
            ->find($id);

        $workerSalaries = WorkerSalary::with(['voucher'])->where('application_id', $id)->get();

        if (!$worker) {
            return response()->json([
                'success' => 'failed',
                'message' => 'Data pekerja tidak ditemukan.',
            ], 404);
        }

        try {
            $datas = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->roles->first()->name,
                    'access_token' => $user->access_token,
                ],
                'detail' => [
                    'id' => $worker->id,
                    'servant_id' => $worker->servant_id,
                    'client' => $worker->employe_id ? $worker->employe->name : $worker->vacancy->user->name,
                    'status' => $worker->status,
                    'salary' => $worker->salary,
                    'work_start_date' => $worker->work_start_date,
                    'servant_detail' => [
                        'id' => $worker->servant->id,
                        'name' => $worker->servant->name,
                        'username' => $worker->servant->username,
                        'email' => $worker->servant->email,
                        'gender'           => $worker->servant->servantDetails->gender ?? 'not_filled',
                        'place_of_birth'   => $worker->servant->servantDetails->place_of_birth ?? '-',
                        'date_of_birth'    => $worker->servant->servantDetails->date_of_birth,
                        'religion'         => $worker->servant->servantDetails->religion ?? '-',
                        'marital_status'   => $worker->servant->servantDetails->marital_status ?? 'not_filled',
                        'children'         => $worker->servant->servantDetails->children ?? 0,
                        'last_education'   => $worker->servant->servantDetails->last_education ?? 'not_filled',
                        'phone'            => $worker->servant->servantDetails->phone ?? '-',
                        'emergency_number' => $worker->servant->servantDetails->emergency_number ?? '-',
                        'address'          => $worker->servant->servantDetails->address ?? '-',
                        'rt'               => $worker->servant->servantDetails->rt,
                        'rw'               => $worker->servant->servantDetails->rw,
                        'village'          => $worker->servant->servantDetails->village,
                        'district'         => $worker->servant->servantDetails->district,
                        'regency'          => $worker->servant->servantDetails->regency,
                        'province'         => $worker->servant->servantDetails->province,
                        'is_bank'          => $worker->servant->servantDetails->is_bank ?? 0,
                        'bank_name'        => $worker->servant->servantDetails->bank_name ?? '-',
                        'account_number'   => $worker->servant->servantDetails->account_number ?? '-',
                        'is_bpjs'          => $worker->servant->servantDetails->is_bpjs ?? 0,
                        'type_bpjs'        => $worker->servant->servantDetails->type_bpjs ?? 'Ketenagakerjaan',
                        'number_bpjs'      => $worker->servant->servantDetails->number_bpjs ?? '-',
                        'photo'            => $worker->servant->servantDetails->photo,
                        'identity_card'    => $worker->servant->servantDetails->identity_card,
                        'family_card'      => $worker->servant->servantDetails->family_card,
                        'working_status'   => $worker->servant->servantDetails->working_status ?? 0,
                        'experience'       => $worker->servant->servantDetails->experience ?? '-',
                        'description'      => $worker->servant->servantDetails->description ?? '-',
                        'is_inval'         => $worker->servant->servantDetails->is_inval ?? 0,
                        'is_stay'          => $worker->servant->servantDetails->is_stay ?? 0,
                        'profession'       => $worker->servant->servantDetails->profession->name ?? null,
                        'skills' => $worker->servant->servantSkills->map(function ($skill) {
                            return [
                                'id' => $skill->id,
                                'user_id' => $skill->user_id,
                                'skill' => $skill->skill,
                                'keahlian' => $skill->level
                            ];
                        }),
                    ],
                ],
                'gaji' => $workerSalaries->makeHidden(['total_salary_majikan', 'payment_majikan_image', 'payment_pembantu_image', 'voucher', 'voucher_id', 'created_at', 'updated_at']),
            ];

            return response()->json([
                'success' => 'success',
                'message' => 'Data detail pekerja.',
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

    public function complaintWork(Request $request, Application $application)
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

            $complaint = Complaint::where('application_id', $application->id)->where('servant_id', $user->id)->first();

            if ($complaint) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Pengaduan pekerjaan sudah dikirimkan. Silakan coba lagi.',
                    'data'    => $complaint
                ], 409);
            }

            $store = Complaint::create([
                'application_id' => $application->id,
                'servant_id' => $user->id,
                'employe_id' => null,
                'message' => $data['message'],
                'status' => 'pending',
            ]);

            if (!$store) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Pengaduan pekerjaan gagal disimpan. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Pengaduan pekerjaan berhasil dikirimkan!',
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
