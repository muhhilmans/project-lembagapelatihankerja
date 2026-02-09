@extends('cms.layouts.main', ['title' => 'Kelola Lowongan'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Kelola Lowongan</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#createModal">
                Tambah
            </a>
            @include('cms.vacancy.modal.create')
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Judul</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->title }}</td>
                                <td>{{ $data->user->employeDetails->address }}</td>
                                <td class="text-center"><span
                                        class="p-2 badge badge-{{ $data->status == 1 ? 'success' : 'danger' }}">{{ $data->status == 1 ? 'Buka' : 'Tutup' }}</span>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-info mb-2 mb-lg-0" href="{{ route('vacancies.show', $data->id) }}">
                                        <i class="fas fa-fw fa-eye"></i>
                                    </a>

                                    <a class="btn btn-warning mb-2 mb-lg-0" href="#" data-toggle="modal"
                                        data-target="#editModal-{{ $data->id }}"><i class="fas fa-fw fa-edit"></i></a>
                                    @include('cms.vacancy.modal.edit', ['vacancy' => $data])

                                    <a class="btn btn-danger" href="#" data-toggle="modal"
                                        data-target="#deleteModal-{{ $data->id }}"><i
                                            class="fas fa-fw fa-trash"></i></a>
                                    @include('cms.vacancy.modal.delete', [
                                            'vacancy' => $data,
                                        ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Archived Data -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Arsip Lowongan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableResult" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Judul</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tanggal Hapus</th>
                            @role('majikan')
                                <th>Aksi</th>
                            @endrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($archives as $archive)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $archive->title }}</td>
                                <td>{{ $archive->user->employeDetails->address }}</td>
                                <td class="text-center"><span class="badge badge-secondary">Diarsipkan</span></td>
                                <td class="text-center">{{ $archive->deleted_at->format('d M Y H:i') }}</td>
                                @role('majikan')
                                    <td class="text-center">
                                        <form action="{{ route('vacancies.restore', $archive->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin mengaktifkan kembali lowongan ini?')">
                                                <i class="fas fa-trash-restore"></i> Restore
                                            </button>
                                        </form>
                                    </td>
                                @endrole
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
