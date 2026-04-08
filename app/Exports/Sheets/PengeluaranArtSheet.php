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

class PengeluaranArtSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
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
        return 'Pengeluaran ke ART';
    }

    public function collection()
    {
        return WorkerSalary::with([
            'application.servant.servantDetails',
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
            'ID ART',
            'Nama ART',
            'Nama Bank',
            'No. Rekening',
            'Nama Rekening',
            'Gaji Disepakati',
            'Fee Platform',
            'Gaji Bersih Ditransfer',
            'Status Transfer',
            'Tanggal Transfer',
            'No. Referensi',
        ];
    }

    public function map($salary): array
    {
        $this->rowNumber++;

        $app = $salary->application;
        $servant = $app->servant;
        $details = $servant->servantDetails ?? null;

        $feePlatform = $salary->total_salary_majikan - $salary->total_salary_pembantu;

        return [
            $this->rowNumber,
            $servant->id ?? '-',
            $servant->name ?? '-',
            $details->bank_name ?? '-',
            $details->account_number ?? '-',
            $details->account_holder_name ?? $servant->name ?? '-',
            'Rp ' . number_format($salary->total_salary, 0, ',', '.'),
            'Rp ' . number_format($feePlatform, 0, ',', '.'),
            'Rp ' . number_format($salary->total_salary_pembantu, 0, ',', '.'),
            ucfirst($salary->payment_pembantu_status ?? 'belum'),
            $salary->payment_pembantu_transfer_at ? $salary->payment_pembantu_transfer_at->format('d/m/Y H:i') : '-',
            $salary->payment_pembantu_ref_number ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastCol}{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

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
