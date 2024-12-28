@extends('cms.layouts.main', ['title' => 'Lamaran Kerja'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Lamaran - Mandiri</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Lowongan</th>
                            <th>Nama Majikan</th>
                            @hasrole('superadmin|admin')
                                <th>Nama Pelamar</th>
                            @endhasrole
                            <th>Tanggal Interview</th>
                            <th>Status</th>
                            @if ($datas->contains(fn($data) => $data->status === 'passed'))
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->vacancy->title }}</td>
                                <td>{{ $data->vacancy->user->name }}</td>
                                @hasrole('superadmin|admin')
                                    <td>{{ $data->servant->name }}</td>
                                @endhasrole
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($data->interview_date ? $data->interview_date : '')->format('d-M-Y') }}
                                </td>
                                <td class="text-center">
                                    <span
                                        class="p-2 badge badge-{{ match ($data->status) {
                                            'accepted' => 'success',
                                            'rejected' => 'danger',
                                            'pending' => 'warning',
                                            'verify' => 'warning',
                                            'interview' => 'info',
                                            default => 'secondary',
                                        } }}">
                                        {{ match ($data->status) {
                                            'accepted' => 'Diterima',
                                            'rejected' => 'Ditolak',
                                            'pending' => 'Pending',
                                            'interview' => 'Interview',
                                            'passed' => 'Lolos Interview',
                                            'choose' => 'Pending Verifikasi',
                                            'verify' => 'Verifikasi',
                                            'contract' => 'Perjanjian',
                                            default => 'Status Tidak Diketahui',
                                        } }}
                                    </span>
                                </td>
                                @if ($data->status === 'passed')
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                            data-target="#passedModal-{{ $data->id }}"><i
                                                class="fas fa-building"></i></a>
                                        @include('cms.application.modal.passed', [
                                            'data' => $data,
                                        ])
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
