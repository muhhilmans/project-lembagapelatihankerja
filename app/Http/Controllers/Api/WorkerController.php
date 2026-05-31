<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Urgency;
use App\Models\Garansi;
use App\Models\Pengaduan;
use App\Models\Application;
use App\Traits\ApiResponse;
use App\Models\WorkerSalary;
use App\Models\ServantDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WorkerController extends Controller
{
    /**
     * Menangani trait ApiResponse untuk respon JSON yang konsisten.
     */
    use ApiResponse;

    /**
     * Mengambil semua data pekerja aktif yang terkait dengan majikan yang terautentikasi.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allWorker(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->input('search');

            $baseQuery = Application::with(['servant', 'employe', 'vacancy'])
                ->where(function ($query) use ($user) {
                    $query->where('employe_id', $user->id)
                        ->orWhereHas('vacancy', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        });
                })
                ->when($search, function ($q) use ($search) {
                    $q->whereHas('servant', function ($s) use ($search) {
                        $s->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                });

            // Pemisahan Kueri: Active dan History
            $activeWorkers = (clone $baseQuery)->where('status', 'accepted')->paginate(10, ['*'], 'active_page');
            $historyWorkers = (clone $baseQuery)->whereIn('status', ['laidoff', 'rejected'])->paginate(10, ['*'], 'history_page');

            $formatData = function($query) {
                return [
                    'id' => $query->id,
                    'servant_id' => $query->servant_id,
                    'status' => $query->status,
                    'salary_type' => $query->salary_type,
                    'work_start_date' => $query->work_start_date,
                    'work_end_date' => $query->work_end_date,
                    'end_reason' => $query->end_reason,
                    'servant_name' => $query->servant->name ?? '-',
                ];
            };

            return response()->json([
                'success' => 'success',
                'message' => 'Data pekerja berhasil dimuat.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->roles->first()->name,
                    ],
                    'datas' => [
                        'data' => $activeWorkers->map($formatData),
                        'pagination' => [
                            'current_page' => $activeWorkers->currentPage(),
                            'total' => $activeWorkers->total(),
                        ]
                    ],
                    'historyDatas' => [
                        'data' => $historyWorkers->map($formatData),
                        'pagination' => [
                            'current_page' => $historyWorkers->currentPage(),
                            'total' => $historyWorkers->total(),
                        ]
                    ]
                ]
            ], 200);

        } catch (\Throwable $th) {
            Log::error("Error allWorker: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat mengambil data.', [], 500);
        }
    }

    /**
     * Mengambil detail pekerja spesifik berdasarkan ID.
     *
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
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
            ->whereIn('status', ['accepted', 'laidoff', 'rejected'])
            ->find($id);

        $workerSalaries = WorkerSalary::where('application_id', $id)->get();

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
                    'salary_type' => $worker->salary_type,
                    'salary' => $worker->salary,
                    'work_start_date' => $worker->work_start_date,
                    'work_end_date' => $worker->work_end_date,
                    'is_infal' => $worker->is_infal,
                    'infal_frequency' => $worker->infal_frequency,
                    'infal_time_in' => $worker->infal_time_in,
                    'infal_time_out' => $worker->infal_time_out,
                    'infal_hourly_rate' => $worker->infal_hourly_rate,
                    'admin_fee' => $worker->admin_fee,
                    'warranty_duration' => $worker->warranty_duration,
                    'deduction_amount' => $worker->deduction_amount,
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
                        'professions'       => $worker->servant->servantDetails?->professions->map(function ($p) {
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


    public function setSalary(Request $request, Application $application)
    {
        // Pastikan application milik majikan yang login
        $user = auth()->user();
        $isOwner = (string) $application->employe_id === (string) $user->id
            || (string) ($application->vacancy?->user_id) === (string) $user->id;

        if (!$isOwner) {
            return $this->errorResponse('Anda tidak memiliki akses ke kontrak ini.', [], 403);
        }

        if (in_array($application->status, ['accepted', 'rejected', 'laidoff'])) {
            return $this->errorResponse("Status kontrak sudah '{$application->status}' dan tidak dapat diubah.", [], 403);
        }

        $validator = Validator::make($request->all(), [
            'salary_type'          => ['required', 'in:contract,fee'],
            // Contract
            'contract_salary'      => ['required_if:salary_type,contract', 'nullable', 'numeric', 'min:0'],
            'admin_fee'            => ['nullable', 'numeric', 'min:0'],
            'contract_start_date'  => ['required_if:salary_type,contract', 'nullable', 'date'],
            'contract_end_date'    => ['nullable', 'date', 'after_or_equal:contract_start_date'],
            'garansi_id'           => ['nullable', 'exists:garansis,id'],
            'garansi_price'        => ['nullable', 'numeric', 'min:0'],
            'warranty_duration'    => ['nullable', 'string'],
            // Fee
            'is_infal'             => ['sometimes', 'boolean'],
            'fee_salary_regular'   => ['nullable', 'numeric', 'min:0'],
            'fee_frequency_regular'=> ['nullable', 'string'],
            'fee_end_date_regular' => ['nullable', 'date'],
            'infal_frequency'      => ['nullable', 'string'],
            'fee_salary_infal'     => ['nullable', 'numeric', 'min:0'],
            'infal_start_date'     => ['nullable', 'date'],
            'infal_end_date'       => ['nullable', 'date'],
            'infal_time_in'        => ['nullable', 'string'],
            'infal_time_out'       => ['nullable', 'string'],
            'infal_hourly_rate'    => ['nullable', 'numeric', 'min:0'],
            // Common
            'deduction_amount'     => ['nullable', 'numeric', 'min:0'],
            'scheme_id'            => ['nullable', 'exists:schemes,id'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();

            $updateData = [
                'salary_type'      => $data['salary_type'],
                'deduction_amount' => $data['deduction_amount'] ?? 0,
                'scheme_id'        => $data['scheme_id'] ?? null,
            ];

            if ($data['salary_type'] === 'contract') {
                $updateData['salary']           = $data['contract_salary'];
                $updateData['admin_fee']        = $data['admin_fee'] ?? null;
                $updateData['work_start_date']  = $data['contract_start_date'];
                $updateData['work_end_date']    = $data['contract_end_date'] ?? null;

                if (!empty($data['garansi_id'])) {
                    $garansi = Garansi::find($data['garansi_id']);
                    $updateData['garansi_id']        = $data['garansi_id'];
                    $updateData['garansi_price']     = $data['garansi_price'] ?? null;
                    $updateData['warranty_duration'] = $garansi?->name ?? $data['warranty_duration'] ?? null;
                } else {
                    $updateData['garansi_id']        = null;
                    $updateData['garansi_price']     = null;
                    $updateData['warranty_duration'] = $data['warranty_duration'] ?? null;
                }

                // Reset field fee
                $updateData['is_infal']         = false;
                $updateData['infal_frequency']  = null;
                $updateData['infal_time_in']    = null;
                $updateData['infal_time_out']   = null;
                $updateData['infal_hourly_rate'] = null;

            } else { // fee
                $isInfal = (bool) ($data['is_infal'] ?? false);
                $updateData['is_infal'] = $isInfal;

                // Reset field contract
                $updateData['admin_fee']         = null;
                $updateData['warranty_duration'] = null;

                if ($isInfal) {
                    $updateData['salary']          = $data['fee_salary_infal'] ?? null;
                    $updateData['infal_frequency'] = $data['infal_frequency'] ?? null;

                    if (($data['infal_frequency'] ?? '') === 'hourly') {
                        $updateData['work_start_date']   = $data['infal_start_date'] ?? null;
                        $updateData['work_end_date']     = null;
                        $updateData['infal_time_in']     = $data['infal_time_in'] ?? null;
                        $updateData['infal_time_out']    = $data['infal_time_out'] ?? null;
                        $updateData['infal_hourly_rate'] = $data['infal_hourly_rate'] ?? null;
                    } else {
                        $updateData['work_start_date']   = $data['infal_start_date'] ?? null;
                        $updateData['work_end_date']     = $data['infal_end_date'] ?? null;
                        $updateData['infal_time_in']     = null;
                        $updateData['infal_time_out']    = null;
                        $updateData['infal_hourly_rate'] = null;
                    }
                } else {
                    $updateData['salary']          = $data['fee_salary_regular'] ?? null;
                    $updateData['infal_frequency'] = $data['fee_frequency_regular'] ?? null;
                    $updateData['infal_time_in']   = null;
                    $updateData['infal_time_out']  = null;
                    $updateData['infal_hourly_rate'] = null;

                    // End date pembantu H+7 dari end date majikan
                    if (!empty($data['fee_end_date_regular'])) {
                        $updateData['work_end_date'] = Carbon::parse($data['fee_end_date_regular'])->addDays(7)->format('Y-m-d');
                    } else {
                        $updateData['work_end_date'] = null;
                    }
                }
            }

            $application->update($updateData);

            // Sama seperti web: jika status masih interview, otomatis naik ke passed
            if ($application->status === 'interview') {
                $application->update(['status' => 'passed']);
            }

            DB::commit();

            return $this->successResponse(
                $application->fresh(['servant:id,name', 'vacancy:id,title', 'scheme']),
                'Pengaturan gaji berhasil disimpan.'
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error setSalary: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat menyimpan pengaturan gaji.', [], 500);
        }
    }

    public function uploadContractFile(Request $request, Application $application)
    {
        $user = auth()->user();
        $isOwner = (string) $application->employe_id === (string) $user->id
            || (string) ($application->vacancy?->user_id) === (string) $user->id;

        if (!$isOwner) {
            return $this->errorResponse('Anda tidak memiliki akses ke kontrak ini.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'work_start_date' => 'required|date',
            'file_contract'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) return $this->validationErrorResponse($validator);

        try {
            $servant  = User::findOrFail($application->servant_id);
            $employer = $application->employe ?? $application->vacancy?->user;

            $employerName = str_replace(' ', '_', $employer?->name ?? 'employer');
            $servantName  = str_replace(' ', '_', $servant->name);
            $prefix       = $application->employe_id ? 'hire' : 'vacancy';

            $directory   = "contracts/{$prefix}_{$employerName}";
            $fileName    = "contract_{$servantName}." . $request->file('file_contract')->getClientOriginalExtension();
            $storagePath = "public/{$directory}";

            if (!Storage::exists($storagePath)) {
                Storage::makeDirectory($storagePath);
            }

            if ($application->file_contract && Storage::exists('public/' . $application->file_contract)) {
                Storage::delete('public/' . $application->file_contract);
            }

            $path = $request->file('file_contract')->storeAs($storagePath, $fileName);

            DB::beginTransaction();

            $application->update([
                'status'          => 'accepted',
                'work_start_date' => $request->work_start_date,
                'file_contract'   => str_replace('public/', '', $path),
            ]);

            $isInfal = (bool) $application->is_infal;

            // Infal boleh kerja di banyak tempat — tidak dikunci
            if (!$isInfal) {
                $servantDetail = ServantDetail::where('user_id', $servant->id)->first();
                if ($servantDetail) {
                    $servantDetail->update(['working_status' => true]);
                }

                Application::where('servant_id', $servant->id)
                    ->where('id', '!=', $application->id)
                    ->whereNotIn('status', ['accepted', 'rejected', 'laidoff'])
                    ->update([
                        'status'         => 'rejected',
                        'notes_rejected' => 'Telah diterima oleh ' . ($employer?->name ?? '-'),
                    ]);
            }

            // Tutup lowongan jika kuota terpenuhi
            if ($application->vacancy_id) {
                $vacancy       = $application->vacancy;
                $acceptedCount = Application::where('vacancy_id', $vacancy->id)->where('status', 'accepted')->count();
                if ($acceptedCount >= $vacancy->limit) {
                    Application::where('vacancy_id', $vacancy->id)
                        ->whereIn('status', ['pending', 'schedule', 'interview', 'passed', 'verify'])
                        ->update(['status' => 'rejected']);
                    $vacancy->update(['status' => false]);
                }
            }

            DB::commit();

            $fresh = $application->fresh(['servant:id,name', 'vacancy:id,title']);
            $fresh->uploaded_at = now()->translatedFormat('l, d F Y H:i:s');

            return $this->successResponse($fresh, 'File kontrak berhasil diunggah. Status kontrak menjadi aktif.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error uploadContractFile: {$th->getMessage()}");
            return $this->errorResponse('Gagal mengunggah file kontrak.', [], 500);
        }
    }

    /**
     * Mengunggah bukti pembayaran majikan.
     *
     * @param Request $request
     * @param Application $application
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadMajikanContract(Request $request, Application $application)
    {
        $user = auth()->user();
        $isOwner = (string) $application->employe_id === (string) $user->id
            || (string) ($application->vacancy?->user_id) === (string) $user->id;

        if (!$isOwner) {
            return $this->errorResponse('Anda tidak memiliki akses ke kontrak ini.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'month'         => 'required|date_format:Y-m',
            'proof_majikan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) return $this->validationErrorResponse($validator);
        if ($application->salary_type !== 'contract') return $this->errorResponse('Tipe gaji bukan kontrak bulanan.');

        $monthDate = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->format('Y-m-d');

        DB::beginTransaction();
        try {
            $salary = WorkerSalary::firstOrCreate(
                ['application_id' => $application->id, 'month' => $monthDate],
                [
                    'presence'              => 0,
                    'total_salary'          => $application->salary,
                    'total_salary_majikan'  => $application->salary + ($application->admin_fee ?? 0),
                    'total_salary_pembantu' => $application->salary,
                ]
            );

            $majikanName = str_replace(' ', '_', ($application->vacancy ? $application->vacancy->user->name : $application->employe->name));
            $servantName = str_replace(' ', '_', $application->servant->name);
            $date        = Carbon::parse($salary->month)->format('M-Y');
            $directory   = "payments/{$majikanName}/{$servantName}";
            $baseFileName = "proof_majikan_contract_{$date}_{$servantName}";

            if ($salary->payment_majikan_image && Storage::disk('public')->exists("payments/{$salary->payment_majikan_image}")) {
                Storage::disk('public')->delete("payments/{$salary->payment_majikan_image}");
            }

            $path = $this->convertAndStoreToWebp($request->file('proof_majikan'), $directory, $baseFileName);
            $salary->update(['payment_majikan_image' => $path]);

            DB::commit();
            return $this->successResponse($salary, 'Berhasil mengupload bukti pembayaran kontrak.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error uploadMajikanContract: {$th->getMessage()}");
            return $this->errorResponse('Kesalahan sistem saat memproses bukti pembayaran kontrak.', [], 500);
        }
    }

    public function uploadMajikanFee(Request $request, Application $application)
    {
        if ($application->salary_type !== 'fee') {
            return $this->errorResponse('Tipe gaji bukan fee/infal.');
        }

        // quantity hanya wajib jika frekuensi infal adalah hourly/daily/weekly
        $needQuantity = in_array($application->infal_frequency, ['hourly', 'daily', 'weekly']);

        $rules = [
            'proof_majikan'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'worker_salary_id' => 'nullable|exists:worker_salaries,id',
            'month'            => 'required|date_format:Y-m',
            'absence_days'     => 'nullable|integer|min:0',
            'absence_reason'   => 'nullable|string|max:255',
            'extra_deduction'  => 'nullable|integer|min:0',
        ];
        if ($needQuantity) {
            $rules['quantity'] = 'required|numeric|min:0.1';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            DB::beginTransaction();

            $data           = $validator->validated();
            $absenceDays    = (int) ($data['absence_days'] ?? 0);
            $extraDeduction = (int) ($data['extra_deduction'] ?? 0);
            $quantity       = $needQuantity ? (float) $data['quantity'] : 1;
            $monthDate      = Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth()->format('Y-m-d');

            $tarifSatuan = $application->salary;
            $gajiPokok   = $tarifSatuan * $quantity;

            $deductionAmount = $application->deduction_amount ?? $tarifSatuan;
            $totalDeduction  = ($absenceDays * (int) $deductionAmount) + $extraDeduction;
            $gajiPokokBersih = max(0, $gajiPokok - $totalDeduction);

            // Hitung admin fee dari scheme->client_data dan mitra_data (array per item)
            $totalSalaryMajikan  = $gajiPokokBersih;
            $totalSalaryPembantu = $gajiPokokBersih;

            if ($application->scheme) {
                $clientFees = 0;
                if (is_array($application->scheme->client_data)) {
                    foreach ($application->scheme->client_data as $fee) {
                        $clientFees += isset($fee['unit']) && $fee['unit'] === '%'
                            ? ($gajiPokokBersih * ($fee['value'] / 100))
                            : ($fee['value'] ?? 0);
                    }
                }
                $totalSalaryMajikan = $gajiPokokBersih + $clientFees;

                $mitraDeductions = 0;
                if (is_array($application->scheme->mitra_data)) {
                    foreach ($application->scheme->mitra_data as $deduction) {
                        $mitraDeductions += isset($deduction['unit']) && $deduction['unit'] === '%'
                            ? ($gajiPokokBersih * ($deduction['value'] / 100))
                            : ($deduction['value'] ?? 0);
                    }
                }
                $totalSalaryPembantu = $gajiPokokBersih - $mitraDeductions;
            }

            $salary = WorkerSalary::firstOrCreate(
                [
                    'id' => $data['worker_salary_id'] ?? null,
                    'application_id' => $application->id,
                    'month' => $monthDate,
                ],
                [
                    'presence' => max(0, $quantity - $absenceDays),
                    'absence' => $absenceDays,
                    'absence_reason' => $data['absence_reason'] ?? null,
                    'extra_deduction' => $extraDeduction,
                    'total_salary' => $gajiPokokBersih,
                    'total_salary_majikan' => $totalSalaryMajikan,
                    'total_salary_pembantu' => $totalSalaryPembantu,
                ]
            );

            // Eksekusi Update jika data bulan ini ternyata sudah ada
            if (!$salary->wasRecentlyCreated) {
                 $salary->update([
                    'presence' => max(0, $quantity - $absenceDays),
                    'absence' => $absenceDays,
                    'absence_reason' => $data['absence_reason'] ?? null,
                    'extra_deduction' => $extraDeduction,
                    'total_salary' => $gajiPokokBersih,
                    'total_salary_majikan' => $totalSalaryMajikan,
                    'total_salary_pembantu' => $totalSalaryPembantu,
                 ]);
            }

            // 7. Upload Bukti Gambar (Tetap Dipertahankan)
            $majikanName = str_replace(' ', '_', ($application->vacancy ? $application->vacancy->user->name : $application->employe->name));
            $servantName = str_replace(' ', '_', $application->servant->name);
            $date = Carbon::parse($salary->month)->format('M-Y');
            $directory = "payments/{$majikanName}/{$servantName}";
            $baseFileName = "proof_majikan_Fee_" . $date . "_{$servantName}";

            if ($salary->payment_majikan_image && Storage::disk('public')->exists("payments/" . $salary->payment_majikan_image)) {
                Storage::disk('public')->delete("payments/" . $salary->payment_majikan_image);
            }

            $path = $this->convertAndStoreToWebp($request->file('proof_majikan'), $directory, $baseFileName);

            $salary->update(['payment_majikan_image' => $path]);

            DB::commit();

            return $this->successResponse($salary, "Berhasil memproses perhitungan potongan dan upload bukti pembayaran Fee.");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error uploadMajikanFee: {$th->getMessage()}");
            return $this->errorResponse('Kesalahan sistem saat memproses gaji fee', $th->getMessage());
        }
    }

    // Fungsi Helper Internal untuk Proses Upload Gaji agar tidak mengulang kode
    private function processUploadMajikan($request, $application, $type)
    {
        try {
            $salary = WorkerSalary::find($request->worker_salary_id);
            if(!$salary) return $this->errorResponse('Data gaji tidak ditemukan');

            $majikanName = str_replace(' ', '_', ($application->vacancy ? $application->vacancy->user->name : $application->employe->name));
            $servantName = str_replace(' ', '_', $application->servant->name);
            $date = Carbon::parse($salary->month)->format('M-Y');
            $directory = "payments/{$majikanName}/{$servantName}";
            $baseFileName = "proof_majikan_{$type}_" . $date . "_{$servantName}";

            if ($salary->payment_majikan_image && Storage::disk('public')->exists("payments/" . $salary->payment_majikan_image)) {
                Storage::disk('public')->delete("payments/" . $salary->payment_majikan_image);
            }

            $path = $this->convertAndStoreToWebp($request->file('proof_majikan'), $directory, $baseFileName);

            DB::transaction(function () use ($salary, $path) {
                $salary->update(['payment_majikan_image' => $path]);
            });

            return $this->successResponse($salary, "Berhasil mengupload bukti pembayaran $type");
        } catch (\Throwable $th) {
            return $this->errorResponse('Kesalahan sistem', $th->getMessage());
        }
    }

    /**
     * Menolak atau menghentikan kontrak pekerja.
     *
     * @param Request $request
     * @param Application $application
     * @return \Illuminate\Http\JsonResponse
     */
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

            try {
                $majikanName = auth()->user()->name;
                $application->servant->notify(new GeneralNotification(
                    title: 'Kontrak Kerja Diakhiri',
                    body: "Kontrak kerja Anda dengan {$majikanName} telah diakhiri (Status: {$datas['status']}).",
                    type: 'contract_ended',
                    data: ['application_id' => $application->id]
                ));
            } catch (\Exception $e) {
                Log::error("Gagal kirim notif rejectWorker: " . $e->getMessage());
            }

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

    /**
     * Mengirimkan pengaduan tentang pekerja.
     *
     * @param Request $request
     * @param Application $application
     * @return \Illuminate\Http\JsonResponse
     */
    public function complaintWorker(Request $request, Application $application)
    {
        $user = auth()->user();

        $isOwner = (string) ($application->employe_id ?? $application->vacancy?->user_id) === (string) $user->id;
        if (!$isOwner) {
            return $this->errorResponse('Anda tidak memiliki akses ke kontrak ini.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'complaint_type_id' => ['required', 'exists:urgencies,id'],
            'description'       => ['required', 'string', 'min:20'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            DB::beginTransaction();

            $urgency = Urgency::find($request->complaint_type_id);

            $complaint = Pengaduan::create([
                'contract_id'       => $application->id,
                'complaint_type_id' => $request->complaint_type_id,
                'urgency_level'     => $urgency->default_urgency ?? 'LOW',
                'reporter_id'       => $user->id,
                'reported_user_id'  => $application->servant_id,
                'description'       => $request->description,
                'status'            => 'open',
            ]);

            DB::commit();

            try {
                $admins = User::role(['admin', 'superadmin'])->get();
                foreach ($admins as $admin) {
                    $admin->notify(new GeneralNotification(
                        title: 'Pengaduan Baru Masuk',
                        body: "{$user->name} mengajukan pengaduan baru dengan urgensi {$complaint->urgency_level}.",
                        type: 'complaint_new',
                        data: ['complaint_id' => $complaint->id]
                    ));
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal kirim notif complaint baru: {$e->getMessage()}");
            }

            return $this->successResponse(
                $complaint->load(['complaintType:id,name,default_urgency', 'reporter:id,name', 'reportedUser:id,name']),
                'Pengaduan pekerja berhasil dikirimkan!',
                201
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("complaintWorker Error: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat mengirimkan aduan.', [], 500);
        }
    }

    /**
     * Mengambil semua pekerjaan untuk pembantu yang terautentikasi.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allWork(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');

        try {
            $baseQuery = Application::with(['servant', 'employe', 'vacancy'])
                ->where('servant_id', $user->id)
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->whereHas('employe', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                          ->orWhereHas('vacancy.user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
                    });
                });

            $activeWorkers  = (clone $baseQuery)->where('status', 'accepted')->paginate(10, ['*'], 'active_page');
            $historyWorkers = (clone $baseQuery)->whereIn('status', ['laidoff', 'rejected'])->paginate(10, ['*'], 'history_page');

            $formatWork = function ($query) {
                return [
                    'id'              => $query->id,
                    'client'          => $query->employe ? $query->employe->name : ($query->vacancy->user->name ?? '-'),
                    'status'          => $query->status,
                    'salary_type'     => $query->salary_type,
                    'salary'          => $query->salary,
                    'interview_date'  => $query->interview_date,
                    'link_interview'  => $query->link_interview,
                    'notes_interview' => $query->notes_interview,
                    'notes_verify'    => $query->notes_verify,
                    'notes_accepted'  => $query->notes_accepted,
                    'notes_rejected'  => $query->notes_rejected,
                    'end_reason'      => $query->end_reason,
                    'file_contract'   => $query->file_contract,
                    'work_start_date' => $query->work_start_date,
                    'work_end_date'   => $query->work_end_date,
                ];
            };

            $datas = [
                'user' => [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'email'    => $user->email,
                    'role'     => $user->roles->first()->name,
                ],
                'datas' => [
                    'data'       => $activeWorkers->map($formatWork),
                    'pagination' => [
                        'current_page' => $activeWorkers->currentPage(),
                        'per_page'     => $activeWorkers->perPage(),
                        'total'        => $activeWorkers->total(),
                        'last_page'    => $activeWorkers->lastPage(),
                        'next_page_url' => $activeWorkers->nextPageUrl(),
                        'prev_page_url' => $activeWorkers->previousPageUrl(),
                    ],
                ],
                'historyDatas' => [
                    'data'       => $historyWorkers->map($formatWork),
                    'pagination' => [
                        'current_page' => $historyWorkers->currentPage(),
                        'per_page'     => $historyWorkers->perPage(),
                        'total'        => $historyWorkers->total(),
                        'last_page'    => $historyWorkers->lastPage(),
                        'next_page_url' => $historyWorkers->nextPageUrl(),
                        'prev_page_url' => $historyWorkers->previousPageUrl(),
                    ],
                ],
            ];

            return response()->json([
                'success' => 'success',
                'message' => 'Data semua pekerjaan.',
                'data'    => $datas,
            ], 200);
        } catch (\Throwable $th) {
            Log::error("message: '{$th->getMessage()}',  file: '{$th->getFile()}',  line: {$th->getLine()}");
            return response()->json([
                'success' => 'failed',
                'message' => 'Terjadi kesalahan saat mengambil data.',
            ], 500);
        }
    }

    /**
     * Mengambil detail pekerjaan spesifik untuk pembantu.
     *
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showWork($id)
    {
        $user = auth()->user();
        $worker = Application::with(['servant', 'employe', 'vacancy'])
            ->where('servant_id', $user->id)
            ->whereIn('status', ['accepted', 'laidoff', 'rejected'])
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
                    'salary_type' => $worker->salary_type,
                    'salary' => $worker->salary,
                    'work_start_date' => $worker->work_start_date,
                    'work_end_date' => $worker->work_end_date,
                    'is_infal' => $worker->is_infal,
                    'infal_frequency' => $worker->infal_frequency,
                    'infal_time_in' => $worker->infal_time_in,
                    'infal_time_out' => $worker->infal_time_out,
                    'infal_hourly_rate' => $worker->infal_hourly_rate,
                    'admin_fee' => $worker->admin_fee,
                    'warranty_duration' => $worker->warranty_duration,
                    'deduction_amount' => $worker->deduction_amount,
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

    /**
     * Mengirimkan pengaduan tentang pekerjaan/majikan.
     *
     * @param Request $request
     * @param Application $application
     * @return \Illuminate\Http\JsonResponse
     */
    public function complaintWork(Request $request, Application $application)
    {
        $user = auth()->user();

        if ((string) $application->servant_id !== (string) $user->id) {
            return $this->errorResponse('Anda tidak memiliki akses ke kontrak ini.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'complaint_type_id' => ['required', 'exists:urgencies,id'],
            'description'       => ['required', 'string', 'min:20'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            DB::beginTransaction();

            $urgency    = Urgency::find($request->complaint_type_id);
            $employerId = $application->employe_id ?? $application->vacancy?->user_id;

            $complaint = Pengaduan::create([
                'contract_id'       => $application->id,
                'complaint_type_id' => $request->complaint_type_id,
                'urgency_level'     => $urgency->default_urgency ?? 'LOW',
                'reporter_id'       => $user->id,
                'reported_user_id'  => $employerId,
                'description'       => $request->description,
                'status'            => 'open',
            ]);

            DB::commit();

            try {
                $admins = User::role(['admin', 'superadmin'])->get();
                foreach ($admins as $admin) {
                    $admin->notify(new GeneralNotification(
                        title: 'Pengaduan Baru Masuk',
                        body: "{$user->name} mengajukan pengaduan baru dengan urgensi {$complaint->urgency_level}.",
                        type: 'complaint_new',
                        data: ['complaint_id' => $complaint->id]
                    ));
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal kirim notif complaint baru: {$e->getMessage()}");
            }

            return $this->successResponse(
                $complaint->load(['complaintType:id,name,default_urgency', 'reporter:id,name', 'reportedUser:id,name']),
                'Pengaduan pekerjaan berhasil dikirimkan!',
                201
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("complaintWork Error: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat mengirimkan aduan.', [], 500);
        }
    }

    public function uploadAdminContract(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'month'      => 'required|date_format:Y-m',
            'proof_admin' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) return $this->validationErrorResponse($validator);
        if ($application->salary_type !== 'contract') return $this->errorResponse('Tipe gaji bukan kontrak bulanan.');

        $monthDate = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->format('Y-m-d');

        DB::beginTransaction();
        try {
            $salary = WorkerSalary::firstOrCreate(
                ['application_id' => $application->id, 'month' => $monthDate],
                [
                    'presence'              => 0,
                    'total_salary'          => $application->salary,
                    'total_salary_majikan'  => $application->salary + ($application->admin_fee ?? 0),
                    'total_salary_pembantu' => $application->salary,
                ]
            );

            $majikanName  = str_replace(' ', '_', ($application->vacancy ? $application->vacancy->user->name : $application->employe->name));
            $servantName  = str_replace(' ', '_', $application->servant->name);
            $date         = Carbon::parse($salary->month)->format('M-Y');
            $directory    = "payments/{$majikanName}/{$servantName}";
            $baseFileName = "proof_admin_contract_{$date}_{$servantName}";

            if ($salary->payment_pembantu_image && Storage::disk('public')->exists("payments/{$salary->payment_pembantu_image}")) {
                Storage::disk('public')->delete("payments/{$salary->payment_pembantu_image}");
            }

            $path = $this->convertAndStoreToWebp($request->file('proof_admin'), $directory, $baseFileName);
            $salary->update(['payment_pembantu_image' => $path]);

            DB::commit();
            return $this->successResponse($salary, 'Berhasil mengupload bukti pembayaran ke pembantu.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error uploadAdminContract: {$th->getMessage()}");
            return $this->errorResponse('Kesalahan sistem saat memproses bukti pembayaran.', [], 500);
        }
    }

    public function uploadAdminFee(Request $request, Application $application, WorkerSalary $salary)
    {
        $validator = Validator::make($request->all(), [
            'proof_admin' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) return $this->validationErrorResponse($validator);

        try {
            $majikanName  = str_replace(' ', '_', ($application->vacancy ? $application->vacancy->user->name : $application->employe->name));
            $servantName  = str_replace(' ', '_', $application->servant->name);
            $date         = Carbon::parse($salary->month)->format('M-Y');
            $directory    = "payments/{$majikanName}/{$servantName}";
            $baseFileName = "proof_admin_{$date}_{$servantName}";

            if ($salary->payment_pembantu_image && Storage::disk('public')->exists("payments/{$salary->payment_pembantu_image}")) {
                Storage::disk('public')->delete("payments/{$salary->payment_pembantu_image}");
            }

            $path = $this->convertAndStoreToWebp($request->file('proof_admin'), $directory, $baseFileName);

            DB::transaction(function () use ($salary, $path) {
                $salary->update(['payment_pembantu_image' => $path]);
            });

            return $this->successResponse($salary, 'Berhasil mengupload bukti pembayaran ke pembantu.');
        } catch (\Throwable $th) {
            Log::error("Error uploadAdminFee: {$th->getMessage()}");
            return $this->errorResponse('Kesalahan sistem saat memproses bukti pembayaran.', [], 500);
        }
    }

    public function verifyMajikanPayment(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'month'  => 'required|date_format:Y-m',
            'action' => 'required|in:verified,rejected',
        ]);

        if ($validator->fails()) return $this->validationErrorResponse($validator);

        try {
            $monthDate = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->format('Y-m-d');

            $salary = WorkerSalary::where('application_id', $application->id)
                ->where('month', $monthDate)
                ->firstOrFail();

            if ($request->action === 'verified') {
                $salary->update([
                    'payment_majikan_status'      => 'verified',
                    'payment_majikan_verified_at' => now(),
                ]);
                return $this->successResponse($salary, 'Pembayaran majikan telah diverifikasi.');
            } else {
                if ($salary->payment_majikan_image && Storage::disk('public')->exists('payments/' . $salary->payment_majikan_image)) {
                    Storage::disk('public')->delete('payments/' . $salary->payment_majikan_image);
                }
                $salary->update([
                    'payment_majikan_status'      => 'rejected',
                    'payment_majikan_image'       => null,
                    'payment_majikan_verified_at' => null,
                ]);
                return $this->successResponse($salary, 'Pembayaran majikan ditolak. Majikan dapat mengupload ulang.');
            }
        } catch (\Throwable $th) {
            Log::error("Error verifyMajikanPayment: {$th->getMessage()}");
            return $this->errorResponse('Kesalahan sistem saat memverifikasi pembayaran.', [], 500);
        }
    }

    private function convertAndStoreToWebp($file, $directory, $baseFileName)
    {
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $fileName = $baseFileName . '.webp';
            $imagePath = $file->getPathname();
            $image = null;

            if ($extension == 'png') {
                $image = @imagecreatefrompng($imagePath);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
            } else {
                $image = @imagecreatefromjpeg($imagePath);
            }

            if ($image) {
                $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.webp';
                imagewebp($image, $tempPath, 80);
                imagedestroy($image);

                $path = $directory . '/' . $fileName;
                Storage::disk('public')->put($path, file_get_contents($tempPath));
                unlink($tempPath);
                return $path;
            }
        }

        $fileName = $baseFileName . '.' . $extension;
        return $file->storeAs($directory, $fileName, 'public');
    }

    public function endContract(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'end_reason' => 'required|string|max:255',
        ]);

        if ($validator->fails()) return $this->validationErrorResponse($validator);

        try {
            DB::beginTransaction();
            $application->update([
                'status' => 'laidoff',
                'work_end_date' => Carbon::now()->format('Y-m-d'),
                'end_reason' => $request->end_reason
            ]);
            DB::commit();

            return $this->successResponse($application, 'Kontrak kerja berhasil diakhiri.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error endContract: {$th->getMessage()}");
            return $this->errorResponse('Gagal mengakhiri kontrak.', [], 500);
        }
    }

    public function extendContract(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'extend_months' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) return $this->validationErrorResponse($validator);

        try {
            DB::beginTransaction();

            if ($application->work_end_date) {
                $endDate = Carbon::parse($application->work_end_date);
            } else {
                $startDate = $application->work_start_date ? Carbon::parse($application->work_start_date) : Carbon::now();
                $endDate = $startDate->copy()->addMonths(12);
            }

            $newEndDate = $endDate->addMonths((int) $request->extend_months);

            $application->update([
                'work_end_date' => $newEndDate->format('Y-m-d'),
            ]);
            DB::commit();

            return $this->successResponse($application, 'Durasi kontrak berhasil diperpanjang selama ' . $request->extend_months . ' bulan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error extendContract: {$th->getMessage()}");
            return $this->errorResponse('Gagal memperpanjang kontrak.', [], 500);
        }
    }

    public function extendWarranty(Request $request, Application $application)
    {
        $user = auth()->user();
        $isOwner = (string) $application->employe_id === (string) $user->id
            || (string) ($application->vacancy?->user_id) === (string) $user->id;

        if (!$isOwner) {
            return $this->errorResponse('Anda tidak memiliki akses ke kontrak ini.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'garansi_id'    => 'required|exists:garansis,id',
            'garansi_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) return $this->validationErrorResponse($validator);

        try {
            $garansiPrice = $request->garansi_price;
            if (!$garansiPrice) {
                $garansi = \App\Models\Garansi::find($request->garansi_id);
                $garansiPrice = $garansi ? $garansi->price : null;
            }

            $application->update([
                'garansi_id'    => $request->garansi_id,
                'garansi_price' => $garansiPrice,
            ]);

            return $this->successResponse(
                $application->fresh(['servant:id,name', 'vacancy:id,title']),
                'Garansi berhasil diperpanjang/diubah.'
            );
        } catch (\Throwable $th) {
            Log::error("Error extendWarranty: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat memperpanjang garansi.', [], 500);
        }
    }

    public function swapServant(Request $request, Application $application)
    {
        $user = auth()->user();
        $isOwner = (string) $application->employe_id === (string) $user->id
            || (string) ($application->vacancy?->user_id) === (string) $user->id;

        if (!$isOwner) {
            return $this->errorResponse('Anda tidak memiliki akses ke kontrak ini.', [], 403);
        }

        try {
            DB::beginTransaction();

            $startDate = Carbon::parse($application->work_start_date);
            $endDate   = $startDate->copy()->addMonths(1);

            $application->update([
                'status'          => 'laidoff',
                'work_end_date'   => $endDate->format('Y-m-d'),
                'notes_rejected'  => 'Diganti dengan pembantu lain. (Tukar Pembantu)',
            ]);

            DB::commit();
            return $this->successResponse($application, 'Pembantu berhasil ditukar. Silakan pekerjakan pembantu pengganti.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error swapServant: {$th->getMessage()}");
            return $this->errorResponse('Gagal melakukan penukaran.', [], 500);
        }
    }
}
