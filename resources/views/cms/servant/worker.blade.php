@extends('cms.layouts.main', ['title' => 'Pekerja'])

@section('content')
    <!-- Page Heading -->
    <div class="mb-4 d-flex justify-content-between align-items-baseline">
        <h1 class="h3 text-gray-800">Daftar Pembantu Bekerja</h1>
        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#downloadModal"><i
                class="fas fa-download"></i></a>
        @include('cms.servant.modal.export')
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Pembantu</th>
                            <th>Nama Majikan</th>
                            <th>Tanggal Bekerja</th>
                            <th>Bank</th>
                            <th>BPJS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->servant->name }}</td>
                                <td>{{ $data->vacancy->user->name }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($data->work_start_date)->format('d-M-Y') }}
                                </td>
                                <td class="text-center">
                                    @if ($data->servant->servantDetails->is_bank == 1)
                                        {{ $data->servant->servantDetails->account_number }}
                                        ({{ $data->servant->servantDetails->bank_name }})
                                    @else
                                        Belum memiliki rekening
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($data->servant->servantDetails->is_bpjs == 1)
                                        {{ $data->servant->servantDetails->number_bpjs }}
                                        ({{ $data->servant->servantDetails->type_bpjs }})
                                    @else
                                        Belum memiliki BPJS
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
