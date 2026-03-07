<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Voucher;
use App\Models\Application;
use App\Models\Salary;
use Illuminate\Support\Str;
use App\Models\WorkerSalary;
use App\Models\Urgency;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WorkerExport;

class WorkerController extends Controller
{
    public function allWorker()
    {
        // Statuses for Majikan and Pembantu (to see progress)
        $statuses = ['accepted', 'review', 'passed', 'verify', 'contract', 'choose'];
        
        // Statuses for Admin (only active workers and termination reviews)
        $statusesAdmin = ['accepted', 'review'];

        $user = auth()->user();
        $role = $user->roles->first()->name;

        // \Illuminate\Support\Facades\Log::info("WorkerController: User {$user->id} Role: {$role}");

        $historyDatas = collect();

        if ($role == 'majikan') {
            $query = Application::with(['servant.servantDetails', 'scheme'])
                ->whereIn('status', $statuses)
                ->where(function ($q) use ($user) {
                    $q->where('employe_id', $user->id)
                        ->orWhereHas('vacancy.user', function ($subQ) use ($user) {
                            $subQ->where('id', $user->id);
                        });
                });
            
            // \Illuminate\Support\Facades\Log::info("Majikan Query Count: " . $query->count());
            $datas = $query->get();

            // Fetch History for Majikan (laidoff, rejected)
            $historyDatas = Application::with(['servant.servantDetails', 'scheme', 'reviews.reviewer'])
                ->whereIn('status', ['laidoff', 'rejected'])
                ->where(function ($q) use ($user) {
                    $q->where('employe_id', $user->id)
                        ->orWhereHas('vacancy.user', function ($subQ) use ($user) {
                            $subQ->where('id', $user->id);
                        });
                })
                ->orderBy('updated_at', 'desc')
                ->get();

        } elseif ($role == 'pembantu') {
            $query = Application::with(['employe.employeDetails', 'scheme'])
                ->whereIn('status', $statuses)
                ->where('servant_id', $user->id);
            
            // \Illuminate\Support\Facades\Log::info("Pembantu Query Count: " . $query->count());
            
            $datas = $query->get();

            // Fetch History for Pembantu (laidoff, rejected)
            $historyDatas = Application::with(['employe.employeDetails', 'vacancy.user', 'scheme', 'reviews.reviewer'])
                ->whereIn('status', ['laidoff', 'rejected'])
                ->where('servant_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->get();

        } else {
            // Admin / Superadmin / Owner
            $datas = Application::with(['servant.servantDetails', 'employe', 'scheme'])
                ->whereIn('status', $statuses)
                ->where(function ($q) {
                    $q->whereNotNull('employe_id')
                      ->orWhereHas('vacancy', function ($subQ) {
                          $subQ->whereNotNull('user_id');
                      });
                })
                ->get();
        }

        $schemas = \App\Models\Scheme::where('is_active', 1)->get();
        $urgencies = Urgency::where('is_active', true)->get();
        $garansiOptions = \App\Models\Garansi::where('is_active', true)->get();

        return view('cms.servant.worker', compact(['datas', 'historyDatas', 'schemas', 'urgencies', 'garansiOptions']));
    }

    public function showWorker(string $id)
    {
        $data = Application::with('reviews.reviewer', 'reviews.reviewee', 'garansi')->findOrFail($id);
        $salaries = WorkerSalary::where('application_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($data->salary_type == 'contract' && $data->garansi_id && $data->work_start_date) {
            $duration = 3; // default garansi duration
            for ($i = 0; $i < $duration; $i++) {
                $monthDate = \Carbon\Carbon::parse($data->work_start_date)->addMonths($i)->format('Y-m-d');
                $price = $data->garansi_price ?? ($data->garansi ? $data->garansi->price : 0);
                \App\Models\WarrantyPayment::firstOrCreate(
                    ['application_id' => $data->id, 'month_number' => $i + 1],
                    [
                        'month_date' => $monthDate,
                        'amount' => $price,
                        'status' => 'pending',
                    ]
                );
            }
        }
        
        $warrantyPayments = \App\Models\WarrantyPayment::where('application_id', $id)->orderBy('month_number')->get();
        
        $garansiOptions = \App\Models\Garansi::where('is_active', true)->get();

        return view('cms.servant.partial.detail-worker', compact(['data', 'salaries', 'garansiOptions', 'warrantyPayments']));
    }

    public function downloadPdf(Request $request)
    {
        $request->validate([
            'select_data' => 'required|string',
        ]);

        $filter = $request->input('select_data');
        $query = Application::where('status', 'accepted');

        if ($filter === 'not_have_bank') {
            $query->whereHas('servant.servantDetails', function ($q) {
                $q->where('is_bank', 0);
            });
        } elseif ($filter === 'not_have_bpjs') {
            $query->whereHas('servant.servantDetails', function ($q) {
                $q->where('is_bpjs', 0);
            });
        } elseif ($filter === 'not_have_account') {
            $query->whereHas('servant.servantDetails', function ($q) {
                $q->where('is_bank', 0)->where('is_bpjs', 0);
            });
        }

        $datas = $query->get();

        if ($filter === 'not_have_bank') {
            $pdf = Pdf::loadView('cms.servant.pdf.export-bank', compact('datas'))
                ->setPaper('a4', 'potrait');
            return $pdf->download('data_pekerja_tidak_memiliki_rekening_' . date('d-M-Y') . '.pdf');
        } elseif ($filter === 'not_have_bpjs') {
            $pdf = Pdf::loadView('cms.servant.pdf.export-bpjs', compact('datas'))
                ->setPaper('a4', 'potrait');
            return $pdf->download('data_pekerja_tidak_memiliki_bpjs' . date('d-M-Y') . '.pdf');
        } elseif ($filter === 'not_have_account') {
            $pdf = Pdf::loadView('cms.servant.pdf.export', compact('datas'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('data_pekerja_tidak_memiliki_rekening_dan_bpjs' . date('d-M-Y') . '.pdf');
        } else {
            $pdf = Pdf::loadView('cms.servant.pdf.export', compact('datas'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('data_pekerja_' . date('d-M-Y') . '.pdf');
        }
    }

    public function exportExcel(Request $request)
    {
        $filter = $request->input('filter_data', 'all');
        return Excel::download(new WorkerExport(auth()->user()->roles->first()->name, auth()->user()->id, $filter), 'data_pekerja_' . date('d-M-Y') . '.xlsx');
    }

    public function presenceWorker(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:applications,id',
            'month' => 'required',
            'presence' => 'required|integer',
            'voucher' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
        }

        $data = $validator->validated();

        $application = Application::findOrFail($id);

        $schemaSalary = optional($application->schemaSalary);

        if ($schemaSalary->bpjs_client == 0) {
            $bpjsClient = 0;
        } else {
            $bpjsClient = 20000;
        }

        if ($schemaSalary->bpjs_mitra == 0) {
            $bpjsMitra = 0;
        } else {
            $bpjsMitra = 20000;
        }

        $voucher = null;
        if (!empty($data['voucher'])) {
            $voucher = Voucher::where('code', $data['voucher'])->first();

            if (!$voucher) {
                return redirect()->back()->with('toast_error', 'Kode voucher tidak ditemukan');
            }

            if (!$voucher->is_active) {
                return redirect()->back()->with('toast_error', 'Kode voucher sudah tidak aktif');
            }

            if ($voucher->expired_date && $voucher->expired_date < Carbon::now()->format('Y-m-d')) {
                return redirect()->back()->with('toast_error', 'Kode voucher sudah tidak berlaku');
            }

            $usedCount = WorkerSalary::where('voucher_id', $voucher->id)->count();
            $usedInApplication = $application->workerSalary()
                ->where('voucher_id', $voucher->id)
                ->count();

            if ($voucher->people_used && $usedCount >= $voucher->people_used) {
                if ($voucher->time_used && $usedInApplication < $voucher->time_used) {
                    // Tidak ada aksi, lanjutkan proses
                } else {
                    return redirect()->back()->with('toast_error', 'Kode voucher telah mencapai batas pengguna');
                }
            }

            if ($voucher->time_used && $usedInApplication >= $voucher->time_used) {
                return redirect()->back()->with('toast_error', 'Kode voucher telah mencapai batas penggunaan pada pembantu ini');
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
            DB::transaction(function () use ($data, $dataSalary, $voucher) {
                WorkerSalary::create([
                    'application_id' => $data['application_id'],
                    'month' => Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth()->format('Y-m-d'),
                    'presence' => $data['presence'],
                    'total_salary' => $dataSalary['total_salary'],
                    'total_salary_majikan' => $dataSalary['total_salary_majikan'],
                    'total_salary_pembantu' => $dataSalary['total_salary_pembantu'],
                    'voucher_id' => $voucher ? $voucher->id : null,
                ]);
            });

            Alert::success('Berhasil', 'Berhasil mengisi kehadiran!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function updatePresenceWorker(Request $request, Application $app, WorkerSalary $salary)
    {
        $validator = Validator::make($request->all(), [
            'presence' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
        }

        $data = $validator->validated();

        $schemaSalary = optional($app->schemaSalary);

        if ($schemaSalary->bpjs_client == 0) {
            $bpjsClient = 0;
        } else {
            $bpjsClient = 20000;
        }

        if ($schemaSalary->bpjs_mitra == 0) {
            $bpjsMitra = 0;
        } else {
            $bpjsMitra = 20000;
        }

        $monthString = substr($salary->month, 0, 7);
        $month = Carbon::createFromFormat('Y-m', $monthString);
        $daysInMonth = $month->daysInMonth;
        $daySalary = $app->salary / $daysInMonth;

        $totalSalary = $data['presence'] * $daySalary;
        $discount = $salary->voucher_id ? ($schemaSalary->adds_client - ($salary->voucher->discount / 100)) : $schemaSalary->adds_client;

        
        $majikanBonus = $totalSalary * $discount;
        $totalSalaryMajikan = ($totalSalary + $majikanBonus) + $bpjsClient;
        
        $addSalaryPembantu = $totalSalary * $schemaSalary->adds_mitra;
        $totalSalaryPembantu = ($totalSalary - $addSalaryPembantu) - $bpjsMitra;

        $dataSalary = [
            'discount' => $discount,
            'day_salary' => ceil($daySalary),
            'total_salary' => ceil($totalSalary),
            'total_salary_majikan' => ceil($totalSalaryMajikan),
            'total_salary_pembantu' => ceil($totalSalaryPembantu),
        ];

        try {
            DB::transaction(function () use ($salary, $data, $dataSalary) {
                $salary->update([
                    'presence' => $data['presence'],
                    'total_salary' => $dataSalary['total_salary'],
                    'total_salary_majikan' => $dataSalary['total_salary_majikan'],
                    'total_salary_pembantu' => $dataSalary['total_salary_pembantu'],
                ]);
            });

            Alert::success('Berhasil', 'Berhasil mengubah kehadiran!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function uploadWarranty(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
        }

        try {
            $wp = \App\Models\WarrantyPayment::findOrFail($id);
            $app = $wp->application;

            $majikanName = str_replace(' ', '_', ($app->vacancy ? $app->vacancy->user->name : $app->employe->name));
            $servantName = str_replace(' ', '_', $app->servant->name);
            $date = Carbon::parse($wp->month_date)->format('M-Y');
            $directory = "warranty_payments/{$majikanName}/{$servantName}";
            $fileName = "proof_warranty_{$date}_{$servantName}." . $request->file('proof_file')->getClientOriginalExtension();
            $storagePath = "public/{$directory}";

            if (!Storage::exists($storagePath)) {
                Storage::makeDirectory($storagePath);
            }

            if ($wp->payment_image && Storage::exists("public/warranty_payments/{$wp->payment_image}")) {
                Storage::delete("public/warranty_payments/{$wp->payment_image}");
            }

            $path = $request->file('proof_file')->storeAs($storagePath, $fileName);

            $wp->update([
                'payment_image' => str_replace('public/warranty_payments/', '', $path),
                'status' => 'waiting',
            ]);

            Alert::success('Berhasil', 'Berhasil mengupload bukti pembayaran garansi!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];
            return view('cms.error', compact('data'));
        }
    }

    public function verifyWarranty(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:paid,pending',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
        }

        try {
            $wp = \App\Models\WarrantyPayment::findOrFail($id);
            $wp->update([
                'status' => $request->status,
                'verified_at' => $request->status == 'paid' ? now() : null,
            ]);

            Alert::success('Berhasil', 'Status pembayaran garansi berhasil diperbarui!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];
            return view('cms.error', compact('data'));
        }
    }

    public function uploadMajikan(Request $request, Application $app, WorkerSalary $salary)
    {
        $validator = Validator::make($request->all(), [
            'proof_majikan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
        }

        $data = $validator->validated();

        try {
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
                    'payment_majikan_status' => 'waiting',
                ]);
            });

        Alert::success('Berhasil', 'Berhasil mengupload bukti pembayaran!');
        return redirect()->back();
    } catch (\Throwable $th) {
        $data = [
            'message' => $th->getMessage(),
            'status' => 400
        ];

        return view('cms.error', compact('data'));
    }
}

public function uploadMajikanContract(Request $request, Application $app)
{
    $validator = Validator::make($request->all(), [
        'month' => 'required',
        'proof_majikan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
    }

    $data = $validator->validated();
    $monthDate = Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth()->format('Y-m-d');

    DB::beginTransaction();
    try {
        $salary = WorkerSalary::firstOrCreate(
            ['application_id' => $app->id, 'month' => $monthDate],
            [
                'presence' => 0,
                'total_salary' => $app->salary,
                'total_salary_majikan' => $app->salary + ($app->admin_fee ?? 0),
                'total_salary_pembantu' => $app->salary,
            ]
        );

        $majikanName = str_replace(' ', '_', ($app->vacancy ? $app->vacancy->user->name : $app->employe->name));
        $servantName = str_replace(' ', '_', $app->servant->name);
        $date = Carbon::parse($salary->month)->format('M-Y');
        $directory = "payments/{$majikanName}/{$servantName}";
        $fileName = "proof_majikan_contract_" . $date . "_{$servantName}." . $request->file('proof_majikan')->getClientOriginalExtension();
        $storagePath = "public/{$directory}";

        if (!Storage::exists($storagePath)) {
            Storage::makeDirectory($storagePath);
        }

        if ($salary->payment_majikan_image && Storage::exists("public/payments/{$salary->payment_majikan_image}")) {
            Storage::delete("public/payments/{$salary->payment_majikan_image}");
        }

        $path = $request->file('proof_majikan')->storeAs($storagePath, $fileName);

        $salary->update([
            'payment_majikan_image' => str_replace('public/payments/', '', $path),
            'payment_majikan_status' => 'waiting',
        ]);

        DB::commit();

        Alert::success('Berhasil', 'Berhasil mengupload bukti pembayaran!');
        return redirect()->back();
    } catch (\Throwable $th) {
        DB::rollBack();
        $data = [
            'message' => $th->getMessage(),
            'status' => 400
        ];

        return view('cms.error', compact('data'));
    }
}

public function uploadMajikanFee(Request $request, Application $app)
{
    // Tentukan apakah perlu input quantity (hourly/daily/weekly)
    $needQuantity = $app->is_infal && in_array($app->infal_frequency, ['hourly', 'daily', 'weekly']);

    $rules = [
        'month' => 'required',
        'proof_majikan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ];
    if ($needQuantity) {
        $rules['quantity'] = 'required|integer|min:1';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
    }

    $data = $validator->validated();
    $monthDate = Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth()->format('Y-m-d');

    DB::beginTransaction();
    try {
        // Hitung gaji pokok: tarif × quantity (untuk hourly/daily/weekly)
        $tarifSatuan = $app->salary;
        $quantity = $needQuantity ? (int) $data['quantity'] : 1;
        $gajiPokok = $tarifSatuan * $quantity;

        $totalSalaryMajikan = $gajiPokok;
        $totalSalaryPembantu = $gajiPokok;

        if ($app->scheme) {
            // Hitung fee klien (tambahan tagihan majikan)
            $clientFees = 0;
            if (is_array($app->scheme->client_data)) {
                foreach ($app->scheme->client_data as $fee) {
                    if (isset($fee['unit']) && $fee['unit'] == '%') {
                        $clientFees += ($gajiPokok * ($fee['value'] / 100));
                    } else {
                        $clientFees += $fee['value'];
                    }
                }
            }
            $totalSalaryMajikan = $gajiPokok + $clientFees;

            // Hitung potongan mitra (pengurangan gaji pembantu)
            $mitraDeductions = 0;
            if (is_array($app->scheme->mitra_data)) {
                foreach ($app->scheme->mitra_data as $deduction) {
                    if (isset($deduction['unit']) && $deduction['unit'] == '%') {
                        $mitraDeductions += ($gajiPokok * ($deduction['value'] / 100));
                    } else {
                        $mitraDeductions += $deduction['value'];
                    }
                }
            }
            $totalSalaryPembantu = $gajiPokok - $mitraDeductions;
        }

        $salary = WorkerSalary::firstOrCreate(
            ['application_id' => $app->id, 'month' => $monthDate],
            [
                'presence' => 0,
                'quantity' => $needQuantity ? $quantity : null,
                'total_salary' => $gajiPokok,
                'total_salary_majikan' => ceil($totalSalaryMajikan),
                'total_salary_pembantu' => ceil($totalSalaryPembantu),
            ]
        );

        // Update quantity dan totals jika record sudah ada
        $salary->update([
            'quantity' => $needQuantity ? $quantity : null,
            'total_salary' => $gajiPokok,
            'total_salary_majikan' => ceil($totalSalaryMajikan),
            'total_salary_pembantu' => ceil($totalSalaryPembantu),
        ]);

        $majikanName = str_replace(' ', '_', ($app->vacancy ? $app->vacancy->user->name : $app->employe->name));
        $servantName = str_replace(' ', '_', $app->servant->name);
        $date = Carbon::parse($salary->month)->format('M-Y');
        $directory = "payments/{$majikanName}/{$servantName}";
        $fileName = "proof_majikan_fee_" . $date . "_{$servantName}." . $request->file('proof_majikan')->getClientOriginalExtension();
        $storagePath = "public/{$directory}";

        if (!Storage::exists($storagePath)) {
            Storage::makeDirectory($storagePath);
        }

        if ($salary->payment_majikan_image && Storage::exists("public/payments/{$salary->payment_majikan_image}")) {
            Storage::delete("public/payments/{$salary->payment_majikan_image}");
        }

        $path = $request->file('proof_majikan')->storeAs($storagePath, $fileName);

        $salary->update([
            'payment_majikan_image' => str_replace('public/payments/', '', $path),
            'payment_majikan_status' => 'waiting',
        ]);

        DB::commit();

        Alert::success('Berhasil', 'Berhasil mengupload bukti pembayaran!');
        return redirect()->back();
    } catch (\Throwable $th) {
        DB::rollBack();
        $data = [
            'message' => $th->getMessage(),
            'status' => 400
        ];

        return view('cms.error', compact('data'));
    }
}

    public function verifyMajikanPayment(Request $request, Application $app)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required',
            'action' => 'required|in:verified,rejected',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
        }

        try {
            $monthDate = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->format('Y-m-d');
            $salary = WorkerSalary::where('application_id', $app->id)
                ->where('month', $monthDate)
                ->firstOrFail();

            if ($request->action === 'verified') {
                $salary->update([
                    'payment_majikan_status' => 'verified',
                    'payment_majikan_verified_at' => now(),
                ]);
                Alert::success('Berhasil', 'Pembayaran majikan telah diverifikasi!');
            } else {
                if ($salary->payment_majikan_image && Storage::exists('public/payments/' . $salary->payment_majikan_image)) {
                    Storage::delete('public/payments/' . $salary->payment_majikan_image);
                }
                $salary->update([
                    'payment_majikan_status' => 'rejected',
                    'payment_majikan_image' => null,
                    'payment_majikan_verified_at' => null,
                ]);
                Alert::success('Ditolak', 'Pembayaran majikan ditolak. Majikan dapat mengupload ulang.');
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];
            return view('cms.error', compact('data'));
        }
    }

    public function uploadAdmin(Request $request, Application $app, WorkerSalary $salary)
    {
        $validator = Validator::make($request->all(), [
            'proof_admin' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
        }

        $data = $validator->validated();

        try {
            $majikanName = str_replace(' ', '_', ($app->vacancy ? $app->vacancy->user->name : $app->employe->name));
            $servantName = str_replace(' ', '_', $app->servant->name);
            $date = Carbon::parse($salary->month)->format('M-Y');
            $directory = "payments/{$majikanName}/{$servantName}";
            $fileName = "proof_admin_" . $date . "_{$servantName}." . $request->file('proof_admin')->getClientOriginalExtension();
            $storagePath = "public/{$directory}";

            if (!Storage::exists($storagePath)) {
                Storage::makeDirectory($storagePath);
            }

            if ($salary->payment_pembantu_image && Storage::exists("payments/{$salary->payment_pembantu_image}")) {
                Storage::delete("payments/{$salary->payment_pembantu_image}");
            }

            $path = $request->file('proof_admin')->storeAs($storagePath, $fileName);

            DB::transaction(function () use ($salary, $path) {
                $salary->update([
                    'payment_pembantu_image' => str_replace('public/payments/', '', $path),
                ]);
            });

        Alert::success('Berhasil', 'Berhasil mengupload bukti pembayaran!');
        return redirect()->back();
    } catch (\Throwable $th) {
        $data = [
            'message' => $th->getMessage(),
            'status' => 400
        ];

        return view('cms.error', compact('data'));
    }
}

public function uploadAdminContract(Request $request, Application $app)
{
    $validator = Validator::make($request->all(), [
        'month' => 'required',
        'proof_admin' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
    }

    $data = $validator->validated();
    $monthDate = Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth()->format('Y-m-d');

    DB::beginTransaction();
    try {
        $salary = WorkerSalary::firstOrCreate(
            ['application_id' => $app->id, 'month' => $monthDate],
            [
                'presence' => 0,
                'total_salary' => $app->salary,
                'total_salary_majikan' => $app->salary + ($app->admin_fee ?? 0),
                'total_salary_pembantu' => $app->salary,
            ]
        );

        $majikanName = str_replace(' ', '_', ($app->vacancy ? $app->vacancy->user->name : $app->employe->name));
        $servantName = str_replace(' ', '_', $app->servant->name);
        $date = Carbon::parse($salary->month)->format('M-Y');
        $directory = "payments/{$majikanName}/{$servantName}";
        $fileName = "proof_admin_contract_" . $date . "_{$servantName}." . $request->file('proof_admin')->getClientOriginalExtension();
        $storagePath = "public/{$directory}";

        if (!Storage::exists($storagePath)) {
            Storage::makeDirectory($storagePath);
        }

        if ($salary->payment_pembantu_image && Storage::exists("public/payments/{$salary->payment_pembantu_image}")) {
            Storage::delete("public/payments/{$salary->payment_pembantu_image}");
        }

        $path = $request->file('proof_admin')->storeAs($storagePath, $fileName);

        $salary->update([
            'payment_pembantu_image' => str_replace('public/payments/', '', $path),
        ]);

        DB::commit();

        Alert::success('Berhasil', 'Berhasil mengupload bukti pembayaran ke Pembantu!');
        return redirect()->back();
    } catch (\Throwable $th) {
        DB::rollBack();
        $data = [
            'message' => $th->getMessage(),
            'status' => 400
        ];

        return view('cms.error', compact('data'));
    }
}

    public function changeSchema(Request $request, Application $app)
    {
        $validator = Validator::make($request->all(), [
            'scheme_id' => 'required|exists:schemes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->first());
        }

        try {
            DB::beginTransaction();

            // Ambil nilai lama untuk dibandingkan
            $oldScheme = $app->scheme_id;

            if ($oldScheme != $request->input('scheme_id')) {
                $app->update([
                    'scheme_id' => $request->input('scheme_id'),
                ]);
            } else {
                return redirect()->back()->with('toast_info', 'Tidak ada perubahan yang dilakukan.');
            }

            DB::commit();

            Alert::success('Berhasil', 'Berhasil mengubah pengaturan skema biaya!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }
    public function updateBank(Request $request, Application $app)
    {
        $validator = Validator::make($request->all(), [
            'is_bank' => 'nullable',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'is_bpjs' => 'nullable',
            'type_bpjs' => 'nullable|string',
            'number_bpjs' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->first());
        }

        try {
            DB::beginTransaction();

            $servantDetails = $app->servant->servantDetails;

            $updateData = [];

            if ($request->has('is_bank')) {
                $updateData['is_bank'] = 1;
                $updateData['bank_name'] = $request->bank_name;
                $updateData['account_number'] = $request->account_number;
            } else {
                $updateData['is_bank'] = 0;
                $updateData['bank_name'] = null;
                $updateData['account_number'] = null;
            }

            if ($request->has('is_bpjs')) {
                $updateData['is_bpjs'] = 1;
                $updateData['type_bpjs'] = $request->type_bpjs;
                $updateData['number_bpjs'] = $request->number_bpjs;
            } else {
                $updateData['is_bpjs'] = 0;
                $updateData['type_bpjs'] = null;
                $updateData['number_bpjs'] = null;
            }

            $servantDetails->update($updateData);

            DB::commit();

            Alert::success('Berhasil', 'Berhasil mengubah data rekening/BPJS!');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function uploadContractWorker(Request $request, Application $app)
    {
        $validator = Validator::make($request->all(), [
            'file_contract' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->first());
        }

        try {
            $servantName = str_replace(' ', '_', $app->servant->name);
            $employerName = str_replace(' ', '_', ($app->vacancy ? $app->vacancy->user->name : $app->employe->name));

            $directory = "contracts/worker_{$employerName}";
            $fileName = "contract_{$servantName}." . $request->file('file_contract')->getClientOriginalExtension();
            $storagePath = "public/{$directory}";

            if (!Storage::exists($storagePath)) {
                Storage::makeDirectory($storagePath);
            }

            // Hapus file kontrak lama jika ada
            if ($app->file_contract && Storage::exists('public/' . $app->file_contract)) {
                Storage::delete('public/' . $app->file_contract);
            }

            $path = $request->file('file_contract')->storeAs($storagePath, $fileName);

            $app->update([
                'file_contract' => str_replace('public/', '', $path),
            ]);

            Alert::success('Berhasil', 'File kontrak berhasil diunggah!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function updateSalaryType(Request $request, string $id)
    {
        $request->validate([
            'salary_type' => ['required', 'in:contract,fee_langsung,infal'],
        ]);

        try {
            $application = Application::findOrFail($id);
            
            if ($request->salary_type == 'contract') {
                $application->salary_type = 'contract';
                $application->is_infal = false;
            } elseif ($request->salary_type == 'fee_langsung') {
                $application->salary_type = 'fee';
                $application->is_infal = false;
            } elseif ($request->salary_type == 'infal') {
                $application->salary_type = 'fee';
                $application->is_infal = true;
            }

            $application->save();

            Alert::success('Berhasil', 'Jenis Penggajian berhasil diperbarui.');
            return redirect()->back();

        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('Error updating salary type: ' . $th->getMessage());
            Alert::error('Gagal', 'Terjadi kesalahan saat memperbarui Jenis Penggajian.');
            return redirect()->back();
        }
    }

    public function extendWarranty(Request $request, Application $app)
    {
        $request->validate([
            'garansi_id' => 'required|exists:garansis,id',
            'garansi_price' => 'nullable|numeric|min:0',
        ]);

        try {
            $garansiPrice = $request->garansi_price;
            if (!$garansiPrice) {
                $garansi = \App\Models\Garansi::find($request->garansi_id);
                $garansiPrice = $garansi ? $garansi->price : null;
            }

            $app->update([
                'garansi_id' => $request->garansi_id,
                'garansi_price' => $garansiPrice,
            ]);

            Alert::success('Berhasil', 'Garansi berhasil diperpanjang/diubah.');
            return redirect()->back();

        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('Error extending warranty: ' . $th->getMessage());
            Alert::error('Gagal', 'Terjadi kesalahan saat memperpanjang garansi.');
            return redirect()->back();
        }
    }

    public function swapServant(Request $request, Application $app)
    {
        try {
            DB::beginTransaction();
            
            // Set the work_end_date to 1 month from work_start_date
            $startDate = Carbon::parse($app->work_start_date);
            $endDate = $startDate->copy()->addMonths(1);
            
            $app->update([
                'work_end_date' => $endDate->format('Y-m-d'),
                'status' => 'laidoff',
                'notes_rejected' => 'Diganti dengan pembantu lain. (Tukar Pembantu)',
            ]);

            DB::commit();

            Alert::success('Berhasil', 'Pembantu berhasil ditukar. Silahkan jadwalkan/pekerjakan pembantu pengganti di lowongan terkait.');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error swapping servant: ' . $th->getMessage());
            Alert::error('Gagal', 'Terjadi kesalahan saat menukar pembantu.');
            return redirect()->back();
        }
    }

    public function endContract(Request $request, Application $app)
    {
        try {
            $app->update([
                'work_end_date' => Carbon::now()->format('Y-m-d'),
                'status' => 'laidoff',
                'notes_rejected' => 'Kontrak diakhiri.',
            ]);

            Alert::success('Berhasil', 'Kontrak berhasil diakhiri hari ini.');
            return redirect()->back();

        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('Error ending contract: ' . $th->getMessage());
            Alert::error('Gagal', 'Terjadi kesalahan saat mengakhiri kontrak.');
            return redirect()->back();
        }
    }
    public function extendContract(Request $request, Application $app)
    {
        $request->validate([
            'extend_months' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();
            
            // If there's an existing end date, extend from there, otherwise extend from start date + 12 months as base
            if ($app->work_end_date) {
                $endDate = Carbon::parse($app->work_end_date);
            } else {
                $startDate = $app->work_start_date ? Carbon::parse($app->work_start_date) : Carbon::now();
                // Default base duration if not set
                $endDate = $startDate->copy()->addMonths(12);
            }
            
            $newEndDate = $endDate->addMonths($request->extend_months);

            $app->update([
                'work_end_date' => $newEndDate->format('Y-m-d'),
            ]);

            DB::commit();

            Alert::success('Berhasil', 'Kontrak berhasil diperpanjang selama ' . $request->extend_months . ' bulan.');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error extending contract: ' . $th->getMessage());
            Alert::error('Gagal', 'Terjadi kesalahan saat memperpanjang kontrak.');
            return redirect()->back();
        }
    }

    public function storeReview(Request $request, Application $application)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal', $validator->messages()->all()[0]);
            return redirect()->back();
        }

        if ($application->status !== 'laidoff') {
            Alert::error('Gagal', 'Ulasan hanya dapat diberikan pada kontrak yang telah selesai atau dibatalkan.');
            return redirect()->back();
        }

        $userId = auth()->id();

        if ($userId !== $application->servant_id && $userId !== $application->employe_id) {
            Alert::error('Gagal', 'Anda tidak memiliki hak untuk mereview kontrak ini.');
            return redirect()->back();
        }

        $existingReview = \App\Models\Review::where('application_id', $application->id)
            ->where('reviewer_id', $userId)
            ->first();

        if ($existingReview) {
            Alert::error('Gagal', 'Anda sudah memberikan review untuk pekerja/majikan ini.');
            return redirect()->back();
        }

        $user = auth()->user();
        if ($user->hasRole('majikan')) {
            $revieweeId = $application->servant_id;
        } elseif ($user->hasRole('pembantu')) {
            $revieweeId = $application->employe_id ?? optional($application->vacancy)->user_id;
        } else {
            Alert::error('Gagal', 'Role Anda tidak diizinkan untuk memberikan review.');
            return redirect()->back();
        }

        try {
            DB::transaction(function () use ($application, $userId, $revieweeId, $request) {
                \App\Models\Review::create([
                    'application_id' => $application->id,
                    'reviewer_id' => $userId,
                    'reviewee_id' => $revieweeId,
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                ]);
            });

            Alert::success('Berhasil', 'Review berhasil dikirim.');
            return redirect()->back();
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('Error storing review (Web): ' . $th->getMessage());
            Alert::error('Gagal', 'Terjadi kesalahan sistem saat menyimpan review.');
            return redirect()->back();
        }
    }
}
