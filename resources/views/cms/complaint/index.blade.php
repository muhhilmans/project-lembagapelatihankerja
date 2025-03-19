@extends('cms.layouts.main', ['title' => 'Pengaduan'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Kelola Pengaduan</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            @hasrole('superadmin|admin|owner|pembantu')
                                <th>Nama Majikan</th>
                            @endhasrole
                            @hasrole('superadmin|admin|owner|majikan')
                                <th>Nama Pembantu</th>
                            @endhasrole
                            <th>Pesan Pengaduan</th>
                            <th>Status</th>
                            @hasrole('superadmin|admin')
                                <th>Aksi</th>
                            @endhasrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                @hasrole('superadmin|admin|owner|pembantu')
                                    <td>{{ $data->employe->name }}</td>
                                @endhasrole
                                @hasrole('superadmin|admin|owner|majikan')
                                    <td>{{ $data->application->servant->name }}</td>
                                @endhasrole
                                <td>{!! $data->message !!}</td>
                                <td class="text-center">
                                    <span
                                        class="p-2 badge badge-{{ match ($data->status) {
                                            'accepted' => 'success',
                                            'rejected' => 'danger',
                                            'process' => 'warning',
                                            default => 'secondary',
                                        } }}">
                                        {{ match ($data->status) {
                                            'accepted' => 'Diterima',
                                            'rejected' => 'Ditolak',
                                            'pending' => 'Pending',
                                            'process' => 'Verifikasi',
                                            default => 'Status Tidak Diketahui',
                                        } }}
                                    </span>
                                </td>
                                @hasrole('superadmin|admin')
                                    <td class="text-center">
                                        @if ($data->status == 'pending')
                                            <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                                data-target="#processModal-{{ $data->id }}"><i class="fas fa-check"></i></a>
                                            @include('cms.complaint.modal.process', [
                                                'data' => $data,
                                            ])
                                        @endif
                                        @if ($data->status == 'process')
                                            <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                                data-target="#acceptModal-{{ $data->id }}"><i
                                                    class="fas fa-check-double"></i></a>
                                            @include('cms.complaint.modal.accept', [
                                                'data' => $data,
                                            ])

                                            <a href="#" class="btn btn-sm btn-danger" data-toggle="modal"
                                                data-target="#rejectedModal-{{ $data->id }}"><i
                                                    class="fas fa-times"></i></a>
                                            @include('cms.complaint.modal.rejected', [
                                                'data' => $data,
                                            ])
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

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/summernote/summernote-bs4.min.css') }}">
@endpush

@push('custom-script')
    <script src="{{ asset('assets/vendor/summernote/summernote-bs4.min.js') }}"></script>
@endpush