<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReconExport implements WithMultipleSheets
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function sheets(): array
    {
        return [
            'Penerimaan dari Majikan' => new Sheets\PenerimaanMajikanSheet($this->month, $this->year),
            'Pengeluaran ke ART' => new Sheets\PengeluaranArtSheet($this->month, $this->year),
            'Rekonsiliasi' => new Sheets\RekonsiliasiSheet($this->month, $this->year),
        ];
    }
}
