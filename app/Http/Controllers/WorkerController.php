<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Voucher;
use App\Models\Application;
use App\Models\Salary;
use Illuminate\Support\Str;
use App\Models\WorkerSalary;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class WorkerController extends Controller
{
    public function allWorker()
    {
        if (auth()->user()->roles->first()->name == 'majikan') {
            $datas = Application::whereIn('status', ['accepted', 'review'])
                ->where(function ($query) {
                    $query->where('employe_id', auth()->user()->id)
                        ->orWhereHas('vacancy.user', function ($q) {
                            $q->where('id', auth()->user()->id);
                        });
                })->get();
        } elseif (auth()->user()->roles->first()->name == 'pembantu') {
            $datas = Application::whereIn('status', ['accepted', 'review'])
                ->where(function ($query) {
                    $query->where('servant_id', auth()->user()->id);
                })->get();
        } else {
            $datas = Application::whereIn('status', ['accepted', 'review'])->get();
        }

        $schemas = Salary::all();

        return view('cms.servant.worker', compact(['datas', 'schemas']));
    }

    public function showWorker(string $id)
    {
        $data = Application::findOrFail($id);
        $salaries = WorkerSalary::where('application_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cms.servant.partial.detail-worker', compact(['data', 'salaries']));
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

        if ($application->schemaSalary->bpjs_client == 0) {
            $bpjsClient = 0;
        } else {
            $bpjsClient = 20000;
        }

        if ($application->schemaSalary->bpjs_mitra == 0) {
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

        if ($app->schemaSalary->bpjs_client == 0) {
            $bpjsClient = 0;
        } else {
            $bpjsClient = 20000;
        }

        if ($app->schemaSalary->bpjs_mitra == 0) {
            $bpjsMitra = 0;
        } else {
            $bpjsMitra = 20000;
        }

        $monthString = substr($salary->month, 0, 7);
        $month = Carbon::createFromFormat('Y-m', $monthString);
        $daysInMonth = $month->daysInMonth;
        $daySalary = $app->salary / $daysInMonth;

        $totalSalary = $data['presence'] * $daySalary;
        $discount = $salary->voucher_id ? ($app->schemaSalary->adds_client - ($salary->voucher->discount / 100)) : $app->schemaSalary->adds_client;

        
        $majikanBonus = $totalSalary * $discount;
        $totalSalaryMajikan = ($totalSalary + $majikanBonus) + $bpjsClient;
        
        $addSalaryPembantu = $totalSalary * $app->schemaSalary->adds_mitra;
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

    public function changeSchema(Request $request, Application $app)
    {
        $validator = Validator::make($request->all(), [
            'schema_salary' => 'required|exists:salaries,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->first());
        }

        try {
            DB::beginTransaction();

            // Ambil nilai lama untuk dibandingkan
            $oldSchema = $app->schema_salary;

            if ($oldSchema != $request->input('schema_salary')) {
                $app->update([
                    'schema_salary' => $request->input('schema_salary'),
                ]);
            } else {
                return redirect()->back()->with('toast_info', 'Tidak ada perubahan yang dilakukan.');
            }

            DB::commit();

            Alert::success('Berhasil', 'Berhasil mengubah pengaturan gaji pekerja!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }
}
