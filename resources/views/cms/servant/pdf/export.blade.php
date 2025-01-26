<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pembantu Bekerja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <h3 class="text-center">Daftar Pembantu Bekerja</h3>
    <h5>Bulan: {{ \Carbon\Carbon::now()->format('F Y') }}</h5>
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Pembantu</th>
                <th class="text-center">Gaji (Dengan Potongan 2,5%)</th>
                <th class="text-center">Nama Bank</th>
                <th class="text-center">Nomor Rekening</th>
                <th class="text-center">Jenis BPJS</th>
                <th class="text-center">Nomor BPJS</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $data->servant->name ?? 'N/A' }}</td>
                    <td class="text-center">
                        @php
                            $workerSalaries = App\Models\WorkerSalary::where('application_id', $data->id)->get();
                            $currentMonth = \Carbon\Carbon::now()->format('F Y');

                            $workerSalary = 'Belum Mengisi Kehadiran';

                            foreach ($workerSalaries as $salary) {
                                if (\Carbon\Carbon::parse($salary->month)->format('F Y') == $currentMonth) {
                                    $workerSalary = 'Rp. ' . number_format($salary->total_salary_pembantu, 0, ',', '.');
                                    break;
                                }
                            }
                        @endphp

                        @if (strpos($workerSalary, 'Rp.') !== false)
                            {!! $workerSalary !!}
                        @else
                            {{ $workerSalary }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($data->servant->servantDetails->is_bank == 1)
                            {{ $data->servant->servantDetails->bank_name }}
                        @else
                            Belum memiliki rekening
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($data->servant->servantDetails->is_bank == 1)
                            {{ $data->servant->servantDetails->account_number }}
                        @else
                            Belum memiliki rekening
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($data->servant->servantDetails->is_bpjs == 1)
                            {{ $data->servant->servantDetails->type_bpjs }}
                        @else
                            Belum memiliki BPJS
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($data->servant->servantDetails->is_bpjs == 1)
                            {{ $data->servant->servantDetails->number_bpjs }}
                        @else
                            Belum memiliki BPJS
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
