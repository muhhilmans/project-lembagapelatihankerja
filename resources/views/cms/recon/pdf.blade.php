<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Rekonsiliasi Keuangan - {{ $monthNames[$month] }} {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1B3A5C;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            color: #1B3A5C;
            margin-bottom: 4px;
        }

        .header h3 {
            font-size: 13px;
            font-weight: normal;
            color: #555;
        }

        .print-date {
            text-align: right;
            font-size: 10px;
            color: #777;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1B3A5C;
            margin: 18px 0 8px 0;
            padding: 5px 8px;
            background: #E8EDF2;
            border-left: 4px solid #1B3A5C;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }

        table thead th {
            background: #1B3A5C;
            color: #fff;
            padding: 6px 5px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #1B3A5C;
        }

        table tbody td {
            padding: 5px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        table tbody tr:nth-child(even) {
            background: #F8F9FA;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge-success {
            color: #155724;
            font-weight: bold;
        }

        .badge-warning {
            color: #856404;
            font-weight: bold;
        }

        .badge-secondary {
            color: #6c757d;
        }

        .badge-danger {
            color: #721c24;
            font-weight: bold;
        }

        .summary-table {
            width: 50%;
            margin-top: 5px;
        }

        .summary-table td {
            padding: 6px 8px;
            font-size: 11px;
        }

        .summary-table .label-col {
            font-weight: bold;
            width: 60%;
        }

        .selisih-ok {
            background: #D4EDDA !important;
            color: #155724;
            font-weight: bold;
        }

        .selisih-bad {
            background: #F8D7DA !important;
            color: #721c24;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #777;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN REKONSILIASI KEUANGAN</h1>
        <h3>Periode: {{ $monthNames[$month] }} {{ $year }}</h3>
    </div>

    <div class="print-date">
        Tanggal cetak: {{ now()->format('d/m/Y H:i') }}
    </div>

    {{-- Tabel 1: Penerimaan dari Majikan --}}
    <div class="section-title">💳 Penerimaan dari Majikan</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama ART</th>
                <th>Nama Majikan</th>
                <th>Bulan</th>
                <th>Nominal</th>
                <th>Metode</th>
                <th>No. Referensi</th>
                <th>Tgl. Verified</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($salaries as $i => $salary)
                @php
                    $app = $salary->application;
                    $namaMajikan = $app->employe->name ?? ($app->vacancy->user->name ?? '-');
                    $namaArt = $app->servant->name ?? '-';
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $namaArt }}</td>
                    <td>{{ $namaMajikan }}</td>
                    <td>{{ $monthNames[$salary->month->month] ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($salary->payment_majikan_amount ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $salary->payment_majikan_method ?? '-' }}</td>
                    <td>{{ $salary->payment_majikan_ref_number ?? '-' }}</td>
                    <td>{{ $salary->payment_majikan_verified_at ? $salary->payment_majikan_verified_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">
                        @if ($salary->payment_majikan_status === 'verified')
                            <span class="badge-success">Verified ✓</span>
                        @elseif ($salary->payment_majikan_status === 'pending')
                            <span class="badge-warning">Pending</span>
                        @else
                            <span class="badge-secondary">Belum Upload</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data untuk bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tabel 2: Pengeluaran ke ART --}}
    <div class="section-title">💸 Pengeluaran ke ART</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama ART</th>
                <th>Bank</th>
                <th>No. Rekening</th>
                <th>Gaji Disepakati</th>
                <th>Fee Platform</th>
                <th>Gaji Bersih</th>
                <th>No. Referensi</th>
                <th>Tgl. Transfer</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($salaries as $i => $salary)
                @php
                    $app = $salary->application;
                    $servant = $app->servant;
                    $details = $servant->servantDetails ?? null;
                    $feePlatform = $salary->total_salary_majikan - $salary->total_salary_pembantu;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $servant->name ?? '-' }}</td>
                    <td>{{ $details->bank_name ?? '-' }}</td>
                    <td>{{ $details->account_number ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($salary->total_salary, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($feePlatform, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($salary->total_salary_pembantu, 0, ',', '.') }}</td>
                    <td>{{ $salary->payment_pembantu_ref_number ?? '-' }}</td>
                    <td>{{ $salary->payment_pembantu_transfer_at ? $salary->payment_pembantu_transfer_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">
                        @if ($salary->payment_pembantu_status === 'sudah')
                            <span class="badge-success">Sudah ✓</span>
                        @else
                            <span class="badge-danger">Belum</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data untuk bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tabel 3: Summary Rekonsiliasi --}}
    <div class="section-title">📊 Ringkasan Rekonsiliasi</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Komponen</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label-col">Total Masuk dari Majikan</td>
                <td class="text-right">Rp {{ number_format($totalMasukMajikan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label-col">Total Keluar ke ART</td>
                <td class="text-right">Rp {{ number_format($totalKeluarArt, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label-col">Fee Platform</td>
                <td class="text-right">Rp {{ number_format($totalFeePlatform, 0, ',', '.') }}</td>
            </tr>
            <tr class="{{ $selisih == 0 ? 'selisih-ok' : 'selisih-bad' }}">
                <td class="label-col">Selisih (harus = 0)</td>
                <td class="text-right">Rp {{ number_format($selisih, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name ?? 'System' }} pada {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>

</html>
