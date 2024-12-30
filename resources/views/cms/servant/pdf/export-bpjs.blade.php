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
