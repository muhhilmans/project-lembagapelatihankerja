<?php

namespace App\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $role;
    protected $userId;
    protected $filter;

    public function __construct($role, $userId, $filter = 'all')
    {
        $this->role = $role;
        $this->userId = $userId;
        $this->filter = $filter;
    }

    public function collection()
    {
        // Status yang sama persis dengan halaman pekerja
        $statuses = ['accepted', 'review', 'passed', 'verify', 'contract', 'choose'];

        $query = Application::with(['scheme', 'servant.servantDetails'])
            ->whereIn('status', $statuses);

        if ($this->role == 'majikan') {
            $query->where(function ($q) {
                $q->where('employe_id', $this->userId)
                    ->orWhereHas('vacancy.user', function ($subQ) {
                        $subQ->where('id', $this->userId);
                    });
            });
        } elseif ($this->role == 'pembantu') {
            $query->where('servant_id', $this->userId);
        }

        // Filter berdasarkan jenis yang dipilih
        switch ($this->filter) {
            case 'contract':
                $query->where('salary_type', 'contract');
                break;
            case 'fee_bulanan':
                $query->where('salary_type', 'fee')
                    ->where(function ($q) {
                        $q->where('infal_frequency', 'monthly')
                            ->orWhere('is_infal', false)
                            ->orWhereNull('is_infal');
                    });
                break;
            case 'fee_mingguan':
                $query->where('salary_type', 'fee')
                    ->where('is_infal', true)
                    ->where('infal_frequency', 'weekly');
                break;
            case 'fee_harian':
                $query->where('salary_type', 'fee')
                    ->where('is_infal', true)
                    ->where('infal_frequency', 'daily');
                break;
            case 'fee_jam':
                $query->where('salary_type', 'fee')
                    ->where('is_infal', true)
                    ->where('infal_frequency', 'hourly');
                break;
            // 'all' → tidak ada filter tambahan
        }

        // Ambil SEMUA data tanpa pagination
        return $query->get();
    }

    public function headings(): array
    {
        $headers = [
            'Nama Pembantu',
            'Gaji',
        ];

        if (in_array($this->role, ['superadmin', 'admin', 'owner'])) {
            $headers[] = 'Bank';
            $headers[] = 'No. Rekening';
            $headers[] = 'BPJS';
            $headers[] = 'No. BPJS';
        }

        return $headers;
    }

    public function map($application): array
    {
        // Hitung gaji dengan potongan menggunakan scheme (sama seperti tampilan tabel)
        $gajiPokok = $application->salary;
        $gajiPembantu = $gajiPokok;

        if ($application->scheme) {
            $mitraDeductions = 0;
            if (is_array($application->scheme->mitra_data)) {
                foreach ($application->scheme->mitra_data as $deduction) {
                    if (isset($deduction['unit']) && $deduction['unit'] == '%') {
                        $mitraDeductions += ($gajiPokok * ($deduction['value'] / 100));
                    } else {
                        $mitraDeductions += $deduction['value'];
                    }
                }
            }
            $gajiPembantu -= $mitraDeductions;
        }

        $data = [
            $application->servant->name ?? '-',
            'Rp. ' . number_format($gajiPembantu, 0, ',', '.'),
        ];

        if (in_array($this->role, ['superadmin', 'admin', 'owner'])) {
            $data[] = $application->servant->servantDetails->bank_name ?? '-';
            $data[] = $application->servant->servantDetails->account_number ?? '-';
            $data[] = $application->servant->servantDetails->type_bpjs ?? '-';
            $data[] = $application->servant->servantDetails->number_bpjs ?? '-';
        }

        return $data;
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
