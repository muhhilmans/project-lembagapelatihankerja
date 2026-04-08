<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Voucher;
use App\Models\Pengaduan; // Updated
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

    /**
     * Menyimpan absensi pekerja dan menghitung gaji.
     *
     * @param Request $request
     * @param Application $application
     * @return \Illuminate\Http\JsonResponse
     */
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

            // try {
            //     $bulan = Carbon::parse($store->month)->translatedFormat('F Y');
            //     NotificationDispatched::dispatch(
            //         "Laporan Absensi & Gaji bulan {$bulan} telah diterbitkan.",
            //         $application->servant_id,
            //         'success'
            //     );
            // } catch (\Exception $e) {
            //     Log::error("Gagal kirim notif presenceWorker: " . $e->getMessage());
            // }

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

    /**
     * Memperbarui absensi pekerja dan menghitung ulang gaji.
     *
     * @param Request $request
     * @param Application $application
     * @param WorkerSalary $salary
     * @return \Illuminate\Http\JsonResponse
     */
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

            // try {
            //     $bulan = Carbon::parse($salary->month)->translatedFormat('F Y');
            //     NotificationDispatched::dispatch(
            //         "Revisi: Data Absensi bulan {$bulan} telah diperbarui oleh Majikan.",
            //         $application->servant_id,
            //         'info'
            //     );
            // } catch (\Exception $e) {
            //     Log::error("Gagal kirim notif updatePresenceWorker: " . $e->getMessage());
            // }

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

    /**
     * Mengunggah bukti pembayaran majikan.
     *
     * @param Request $request
     * @param Application $application
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadMajikanContract(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'proof_majikan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'worker_salary_id' => 'required|exists:worker_salaries,id'
        ]);

        if ($validator->fails()) return $this->errorResponse($validator->messages()->all()[0]);
        if ($application->salary_type !== 'contract') return $this->errorResponse('Tipe gaji bukan kontrak bulanan.');

        return $this->processUploadMajikan($request, $application, 'Contract');
    }

    public function uploadMajikanFee(Request $request, Application $application)
    {
        // 1. Penambahan Validation Rules Baru [cite: 85]
        // Jika skema fee, input absence_days, absence_reason, dan extra_deduction diizinkan dan dicek [cite: 86]
        $validator = Validator::make($request->all(), [
            'proof_majikan'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'worker_salary_id' => 'nullable|exists:worker_salaries,id',
            'month'            => 'required|date_format:Y-m',
            'quantity'         => 'required|integer|min:1',
            'absence_days'     => 'nullable|integer|min:0',
            'absence_reason'   => 'nullable|string|max:255',
            'extra_deduction'  => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        if ($application->salary_type !== 'fee') {
            return $this->errorResponse('Tipe gaji bukan fee/infal.');
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();
            $absenceDays = $data['absence_days'] ?? 0;
            $extraDeduction = $data['extra_deduction'] ?? 0;
            $quantity = $data['quantity']; // Mendeteksi harian/mingguan [cite: 89]
            $monthDate = Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth()->format('Y-m-d');

            // 2. Perbaikan Formula Kalkulasi Dasar [cite: 87]
            // Perhitungan Gaji Pokok Awal dilakukan terpisah [cite: 90]
            $tarifSatuan = $application->salary;
            $gajiPokok = $tarifSatuan * $quantity;

            // 3. Implementasi Rumus Potongan Baru [cite: 91]
            // Mengalikan absen dengan tarif potongan, ditambah jumlah kasbon [cite: 94]
            $deductionAmount = $application->deduction_amount ?? $tarifSatuan; // Fallback jika deduction_amount kosong
            $totalDeduction = ($absenceDays * (int)$deductionAmount) + $extraDeduction; // [cite: 93]

            // 4. Bugfix Pencegahan Minus [cite: 95]
            // Jika potongan lebih besar dari gaji, pekerja dicatat mendapat gaji 0 [cite: 97]
            $gajiPokokBersih = max(0, $gajiPokok - $totalDeduction); // Menggunakan fungsi max(0, ...) [cite: 95]

            // 5. Sinkronisasi dengan Skema Klien/Mitra (Admin Fee) [cite: 98]
            // Persentase dihitung dari gaji bersih pembantu, bukan kotor [cite: 100]
            // (Asumsi mengambil data skema dari relasi yang ada)
            $schemaSalary = clone $application->schemaSalary;
            $addsClient = $schemaSalary ? $schemaSalary->adds_client : 0;
            $addsMitra  = $schemaSalary ? $schemaSalary->adds_mitra : 0;

            $totalSalaryMajikan  = $gajiPokokBersih + ($gajiPokokBersih * $addsClient);
            $totalSalaryPembantu = $gajiPokokBersih - ($gajiPokokBersih * $addsMitra);

            // 6. Operasi Database Transaction [cite: 101]
            // Menyimpan status menggunakan firstOrCreate dan update dari Eloquent [cite: 102]
            $salary = WorkerSalary::firstOrCreate(
                [
                    'id' => $data['worker_salary_id'] ?? null,
                    'application_id' => $application->id,
                    'month' => $monthDate,
                ],
                [
                    'presence' => max(0, $quantity - $absenceDays),
                    'absence' => $absenceDays, // Kolom digabung dan disimpan [cite: 103]
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
                NotificationDispatched::dispatch(
                    "PENTING: Kontrak kerja Anda dengan {$majikanName} telah diakhiri (Status: {$datas['status']}).",
                    $application->servant_id,
                    'warning'
                );
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
        $validator = Validator::make($request->all(), [
            'message' => ['required'],
            'urgency_level' => ['required', 'in:LOW,MEDIUM,HIGH,CRITICAL'], // Wajib ada
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

            $complaint = Pengaduan::where('contract_id', $application->id)
                ->where('reporter_id', $user->id)
                ->first();

            if ($complaint) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Pengaduan pekerja sudah dikirimkan. Silakan coba lagi.',
                    'data'    => $complaint
                ], 409);
            }

            $store = Pengaduan::create([
                'contract_id' => $application->id,
                'reporter_id' => $user->id,
                'reported_user_id' => $application->servant_id, // (Atau $employerId untuk complaintWork)
                'description' => $data['message'],
                'urgency_level' => $data['urgency_level'], // Masukkan datanya ke DB
                'status' => 'open',
            ]);

            if (!$store) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Pengaduan pekerja gagal disimpan. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

            // Notify...

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

    /**
     * Mengirimkan pengaduan tentang pekerjaan/majikan.
     *
     * @param Request $request
     * @param Application $application
     * @return \Illuminate\Http\JsonResponse
     */
    public function complaintWork(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required'],
            'urgency_level' => ['required', 'in:LOW,MEDIUM,HIGH,CRITICAL'], // Wajib ada
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

            $complaint = Pengaduan::where('contract_id', $application->id)
                ->where('reporter_id', $user->id)
                ->first();

            if ($complaint) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Pengaduan pekerjaan sudah dikirimkan. Silakan coba lagi.',
                    'data'    => $complaint
                ], 409);
            }

            $employerId = $application->employe_id ?? ($application->vacancy ? $application->vacancy->user_id : null);

            $store = Pengaduan::create([
                'contract_id' => $application->id,
                'reporter_id' => $user->id,
                'reported_user_id' => $application->servant_id, // (Atau $employerId untuk complaintWork)
                'description' => $data['message'],
                'urgency_level' => $data['urgency_level'], // Masukkan datanya ke DB
                'status' => 'open',
            ]);

            if (!$store) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Pengaduan pekerjaan gagal disimpan. Silakan coba lagi.'
                ], 502);
            }

            DB::commit();

             // Notify...

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
            'new_work_end_date' => 'required|date|after:today',
        ]);

        if ($validator->fails()) return $this->validationErrorResponse($validator);

        try {
            DB::beginTransaction();
            $application->update([
                'work_end_date' => $request->new_work_end_date
            ]);
            DB::commit();

            return $this->successResponse($application, 'Durasi kontrak berhasil diperpanjang.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error extendContract: {$th->getMessage()}");
            return $this->errorResponse('Gagal memperpanjang kontrak.', [], 500);
        }
    }

    public function swapServant(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'new_servant_id' => 'required|exists:users,id',
            'swap_reason' => 'required|string',
        ]);

        if ($validator->fails()) return $this->validationErrorResponse($validator);

        // Validasi garansi masih aktif
        $warrantyEndsAt = Carbon::parse($application->work_start_date)->addMonths($application->warranty_duration);
        if (Carbon::now()->greaterThan($warrantyEndsAt)) {
            return $this->errorResponse('Masa garansi penukaran pembantu sudah habis.', [], 403);
        }

        try {
            DB::beginTransaction();

            // 1. Akhiri kontrak pembantu lama
            $application->update([
                'status' => 'laidoff',
                'work_end_date' => Carbon::now()->format('Y-m-d'),
                'end_reason' => 'Ditukar (Swap): ' . $request->swap_reason
            ]);

            // 2. Buat kontrak baru untuk pembantu pengganti dengan sisa waktu/garansi
            $newApp = Application::create([
                'servant_id' => $request->new_servant_id,
                'employe_id' => $application->employe_id,
                'vacancy_id' => $application->vacancy_id,
                'status' => 'accepted',
                'salary_type' => $application->salary_type,
                'salary' => $application->salary,
                'warranty_duration' => $application->warranty_duration, // Mewarisi durasi garansi awal
                'work_start_date' => Carbon::now()->format('Y-m-d'),
                'work_end_date' => $application->work_end_date, // Mewarisi target selesai
            ]);

            DB::commit();
            return $this->successResponse($newApp, 'Pembantu berhasil ditukar menggunakan Garansi.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error swapServant: {$th->getMessage()}");
            return $this->errorResponse('Gagal melakukan penukaran.', [], 500);
        }
    }
}


