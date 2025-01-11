@extends('cms.layouts.main', ['title' => 'Blog'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Kelola Blog</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#createModal">
                Tambah
            </a>
            @include('cms.blog.modal.create')
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->title }}</td>
                                <td class="text-center">{{ $data->category ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <img src="{{ route('getImage', ['path' => 'blogs', 'imageName' => $data->image]) }}" alt="" width="100">
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-info" href="#" data-toggle="modal" data-target="#editModal-{{ $data->id }}">
                                        <i class="fas fa-fw fa-edit"></i>
                                    </a>
                                    
                                    <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#deleteModal-{{ $data->id }}">
                                        <i class="fas fa-fw fa-trash"></i>
                                    </a>
                                    @include('cms.blog.modal.delete', ['blog' => $data])
                                </td>
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
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">
@endpush

@push('custom-script')
    <script src="{{ asset('assets/vendor/summernote/summernote-bs4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
@endpush