<?php

namespace App\Exports\Sheets;

use App\Models\WorkerSalary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenerimaanMajikanSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $month;
    protected $year;
    private $rowNumber = 0;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function title(): string
    {
        return 'Penerimaan dari Majikan';
    }

    public function collection()
    {
        return WorkerSalary::with([
            'application.servant',
            'application.employe',
            'application.vacancy.user',
        ])
            ->whereMonth('month', $this->month)
            ->whereYear('month', $this->year)
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Majikan',
            'Bulan',
            'Nominal Dibayar',
            'Metode',
            'No. Referensi',
            'Status',
            'Tanggal Verified',
        ];
    }

    public function map($salary): array
    {
        $this->rowNumber++;

        $app = $salary->application;
        $namaMajikan = $app->employe->name ?? $app->vacancy->user->name ?? '-';

        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return [
            $this->rowNumber,
            $namaMajikan,
            $bulanNames[$salary->month->month] ?? '-',
            $salary->payment_majikan_amount ? 'Rp ' . number_format($salary->payment_majikan_amount, 0, ',', '.') : '-',
            $salary->payment_majikan_method ?? '-',
            $salary->payment_majikan_ref_number ?? '-',
            ucfirst($salary->payment_majikan_status ?? 'belum upload'),
            $salary->payment_majikan_verified_at ? $salary->payment_majikan_verified_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        // Border semua data
        $sheet->getStyle("A1:{$lastCol}{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Header styling
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

        return [];
    }
}
