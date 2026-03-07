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
        <div class="card-body">
            @role('superadmin|admin')
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
            @else
                <div class="row">
                    @forelse($datas as $data)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                                <div class="card-body text-left">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title font-weight-bold text-dark mb-0 text-truncate" style="max-width: 75%;">{{ $data->title }}</h5>
                                        <span class="badge badge-{{ $data->status == 1 ? 'success' : 'danger' }} px-3 py-2" style="border-radius: 20px;">
                                            {{ $data->status == 1 ? 'Buka' : 'Tutup' }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-map-marker-alt mr-1 text-danger"></i> {{ $data->user->employeDetails->address }}
                                    </p>
                                    
                                    <div class="bg-light p-3 rounded mb-3">
                                        @php
                                            $acceptedCount = $data->applications()->where('status', 'accepted')->count();
                                        @endphp
                                        <div class="d-flex justify-content-between text-dark mb-1">
                                            <span class="font-weight-bold" style="font-size: 0.95rem;">Dibutuhkan</span>
                                            <span class="font-weight-bold text-primary">{{ $data->limit }} Orang</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-muted small">
                                            <span>Progress Terisi</span>
                                            <span>{{ $acceptedCount }}/{{ $data->limit }} Terpenuhi</span>
                                        </div>
                                    </div>

                                </div>
                                <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                                    <div class="row mx-0">
                                        <div class="col-6 pl-0 pr-1">
                                            <a class="btn btn-info btn-block btn-sm mb-2" style="border-radius: 8px;" href="{{ route('vacancies.show', $data->id) }}">
                                                <i class="fas fa-fw fa-eye mr-1"></i> Detail
                                            </a>
                                        </div>
                                        <div class="col-6 pr-0 pl-1">
                                            <a class="btn btn-warning btn-block btn-sm mb-2 text-white" style="border-radius: 8px;" href="#" data-toggle="modal" data-target="#editModal-{{ $data->id }}">
                                                <i class="fas fa-fw fa-edit mr-1"></i> Edit
                                            </a>
                                            @include('cms.vacancy.modal.edit', ['vacancy' => $data])
                                        </div>
                                        <div class="col-12 px-0">
                                            <a class="btn btn-outline-danger btn-block btn-sm" style="border-radius: 8px;" href="#" data-toggle="modal" data-target="#deleteModal-{{ $data->id }}">
                                                <i class="fas fa-fw fa-trash mr-1"></i> Hapus
                                            </a>
                                            @include('cms.vacancy.modal.delete', ['vacancy' => $data])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <img src="{{ asset('assets/img/undraw_empty.svg') }}" alt="Kosong" style="width: 150px; opacity: 0.5;" class="mb-3">
                            <p class="text-muted">Belum ada lowongan pekerjaan yang dibuat.</p>
                        </div>
                    @endforelse
                </div>
            @endrole
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
                                @if($archive->isLimitReached())
                                    <td class="text-center"><span class="badge badge-success px-3 py-2" style="border-radius: 12px;">Terpenuhi</span></td>
                                @else
                                    <td class="text-center"><span class="badge badge-secondary px-3 py-2" style="border-radius: 12px;">Diarsipkan</span></td>
                                @endif
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
