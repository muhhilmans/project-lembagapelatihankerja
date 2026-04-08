<?php

namespace App\Http\Controllers;

use App\Models\WorkerSalary;
use App\Exports\ReconExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReconController extends Controller
{
    private function getMonthNames(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $salaries = WorkerSalary::with([
            'application.servant.servantDetails',
            'application.employe',
            'application.vacancy.user',
            'application.scheme',
            'voucher'
        ])
            ->whereMonth('month', $month)
            ->whereYear('month', $year)
            ->get();

        $totalMasukMajikan = $salaries
            ->where('payment_majikan_status', 'verified')
            ->sum('payment_majikan_amount');

        $totalKeluarArt = $salaries
            ->where('payment_pembantu_status', 'sudah')
            ->sum('payment_pembantu_amount');

        $totalFeePlatform = $salaries->sum(function ($s) {
            return $s->total_salary_majikan - $s->total_salary_pembantu;
        });

        $selisih = $totalMasukMajikan - $totalKeluarArt - $totalFeePlatform;

        $monthNames = $this->getMonthNames();
        $title = 'Rekonsiliasi Keuangan';

        return view('cms.recon.index', compact(
            'salaries',
            'month',
            'year',
            'totalMasukMajikan',
            'totalKeluarArt',
            'totalFeePlatform',
            'selisih',
            'monthNames',
            'title'
        ));
    }

    public function exportExcel(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        return Excel::download(
            new ReconExport($month, $year),
            'rekonsiliasi_' . $month . '_' . $year . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $salaries = WorkerSalary::with([
            'application.servant.servantDetails',
            'application.employe',
            'application.vacancy.user',
            'application.scheme',
            'voucher'
        ])
            ->whereMonth('month', $month)
            ->whereYear('month', $year)
            ->get();

        $totalMasukMajikan = $salaries
            ->where('payment_majikan_status', 'verified')
            ->sum('payment_majikan_amount');

        $totalKeluarArt = $salaries
            ->where('payment_pembantu_status', 'sudah')
            ->sum('payment_pembantu_amount');

        $totalFeePlatform = $salaries->sum(function ($s) {
            return $s->total_salary_majikan - $s->total_salary_pembantu;
        });

        $selisih = $totalMasukMajikan - $totalKeluarArt - $totalFeePlatform;

        $monthNames = $this->getMonthNames();

        $pdf = Pdf::loadView('cms.recon.pdf', compact(
            'salaries',
            'month',
            'year',
            'totalMasukMajikan',
            'totalKeluarArt',
            'totalFeePlatform',
            'selisih',
            'monthNames'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('rekonsiliasi_' . $month . '_' . $year . '.pdf');
    }
}
