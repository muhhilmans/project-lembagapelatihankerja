@extends('cms.layouts.main', ['title' => 'Pekerja'])

@section('content')
    <!-- Page Heading -->
    <div class="mb-4 d-flex justify-content-between align-items-baseline">
        <h1 class="h3 text-gray-800">Daftar Pembantu Bekerja</h1>
        @hasrole('superadmin|admin|owner')
            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#downloadModal"><i
                    class="fas fa-download"></i></a>
            @include('cms.servant.modal.export')
        @endhasrole
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
                            @hasrole('superadmin|admin|owner')
                                <th>Nama Majikan</th>
                            @endhasrole
                            <th>Tanggal Bekerja</th>
                            <th>Status</th>
                            <th>Bank</th>
                            @hasrole('superadmin|admin|owner')
                                <th>BPJS</th>
                            @endhasrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->servant->name }}</td>
                                @hasrole('superadmin|admin|owner')
                                    <td>{{ $data->vacancy->user->name }}</td>
                                @endhasrole
                                <td class="text-center">{{ \Carbon\Carbon::parse($data->work_start_date)->format('d-M-Y') }}
                                </td>
                                <td class="text-center">
                                    <span
                                        class="p-2 badge badge-{{ match ($data->status) {
                                            'accepted' => 'success',
                                            'rejected' => 'danger',
                                            'laidoff' => 'danger',
                                            'pending' => 'warning',
                                            'interview' => 'info',
                                            'schedule' => 'info',
                                            'verify' => 'success',
                                            'contract' => 'success',
                                            default => 'secondary',
                                        } }}">
                                        {{ match ($data->status) {
                                            'accepted' => 'Diterima',
                                            'rejected' => 'Ditolak',
                                            'laidoff' => 'Diberhentikan',
                                            'pending' => 'Pending',
                                            'schedule' => 'Penjadwalan',
                                            'interview' => 'Interview',
                                            'passed' => 'Lolos Interview',
                                            'choose' => 'Verifikasi',
                                            'verify' => 'Persiapan Kerja',
                                            'contract' => 'Perjanjian',
                                            default => 'Status Tidak Diketahui',
                                        } }}
                                    </span>
                                </td>
                                @hasrole('majikan')
                                    <td class="text-center">
                                        123123123 (BCA)
                                    </td>
                                @endhasrole
                                @hasrole('superadmin|admin|owner')
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
                                @endhasrole
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
