@extends('cms.layouts.main', ['title' => 'Gaji'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Kelola Pengaturan Gaji</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        @hasrole('superadmin|admin')
            <div class="card-header py-3">
                <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#createModal">
                    Tambah
                </a>
                @include('cms.salary.modal.create')
            </div>
        @endhasrole
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Tambahan Client</th>
                            <th>BPJS Client</th>
                            <th>Potongan Mitra</th>
                            <th>BPJS Mitra</th>
                            @hasrole('superadmin|admin')
                                <th>Aksi</th>
                            @endhasrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $data->adds_client }}</td>
                                <td>{{ $data->bpjs_client == 1 ? 'Ya' : 'Tidak' }}</td>
                                <td>{{ $data->adds_mitra }}</td>
                                <td>{{ $data->bpjs_mitra == 1 ? 'Ya' : 'Tidak' }}</td>
                                @hasrole('superadmin|admin')
                                    <td>
                                        <a class="btn btn-sm btn-warning mb-1 mb-lg-0" href="#" data-toggle="modal"
                                            data-target="#editModal-{{ $data->id }}">
                                            <i class="fas fa-fw fa-edit"></i>
                                        </a>
                                        @include('cms.salary.modal.edit', ['salary' => $data])

                                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal"
                                            data-target="#deleteModal-{{ $data->id }}">
                                            <i class="fas fa-fw fa-trash"></i>
                                        </a>
                                        @include('cms.salary.modal.delete', ['salary' => $data])
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
