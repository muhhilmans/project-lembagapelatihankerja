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
        th, td {
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
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Pembantu</th>
                <th class="text-center">Nama Bank</th>
                <th class="text-center">Nomor Rekening</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $data->servant->name ?? 'N/A' }}</td>
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
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
