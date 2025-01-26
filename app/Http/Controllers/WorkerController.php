<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Application;
use App\Models\Voucher;
use App\Models\WorkerSalary;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
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

        return view('cms.servant.worker', compact('datas'));
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
        $discount = $voucher ? (0.075 - ($voucher->discount / 100)) : 0.075;
        
        $majikanBonus = $totalSalary * $discount;
        $totalSalaryMajikan = ($totalSalary + $majikanBonus) + 20000;
        
        $addSalaryPembantu = $totalSalary * 0.025;
        $totalSalaryPembantu = ($totalSalary - $addSalaryPembantu) - 20000;

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
}
