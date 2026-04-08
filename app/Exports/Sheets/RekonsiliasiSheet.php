<?php

namespace App\Exports\Sheets;

use App\Models\WorkerSalary;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekonsiliasiSheet implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    protected $month;
    protected $year;
    private $selisih = 0;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function title(): string
    {
        return 'Rekonsiliasi';
    }

    public function headings(): array
    {
        return ['Komponen', 'Nominal'];
    }

    public function array(): array
    {
        $salaries = WorkerSalary::whereMonth('month', $this->month)
            ->whereYear('month', $this->year)
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

        $this->selisih = $totalMasukMajikan - $totalKeluarArt - $totalFeePlatform;

        return [
            ['Total Masuk dari Majikan', 'Rp ' . number_format($totalMasukMajikan, 0, ',', '.')],
            ['Total Keluar ke ART', 'Rp ' . number_format($totalKeluarArt, 0, ',', '.')],
            ['Fee Platform', 'Rp ' . number_format($totalFeePlatform, 0, ',', '.')],
            ['Selisih (harus = 0)', 'Rp ' . number_format($this->selisih, 0, ',', '.')],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = 'B';

        // Border
        $sheet->getStyle("A1:{$lastCol}5")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Header
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1B3A5C'],
            ],
        ]);

        // Selisih row (row 5) — green if 0, red if not
        $selisihColor = $this->selisih == 0 ? '28A745' : 'DC3545';
        $sheet->getStyle("A5:{$lastCol}5")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => $selisihColor],
            ],
        ]);

        return [];
    }
}
