@extends('cms.layouts.main', ['title' => 'Profesi'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Kelola Profesi</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#createModal">
                Tambah
            </a>
            @include('cms.profession.modal.create')
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Profesi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($professions as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <a href="#" class="text-secondary" data-toggle="modal" data-target="#draftModal-{{ $data->id }}">{{ $data->name }}</a>
                                    @include('cms.profession.modal.draft', ['profession' => $data])
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-primary mb-2 mb-lg-0" data-toggle="modal" data-target="#draftModal-{{ $data->id }}"><i class="fas fa-fw fa-file-alt"></i></a>
                                    @include('cms.profession.modal.draft', ['profession' => $data])

                                    <a class="btn btn-warning mb-2 mb-lg-0" href="#" data-toggle="modal"
                                        data-target="#editModal-{{ $data->id }}"><i class="fas fa-fw fa-edit"></i></a>
                                    @include('cms.profession.modal.edit', ['profession' => $data])

                                    <a class="btn btn-danger" href="#" data-toggle="modal"
                                        data-target="#deleteModal-{{ $data->id }}"><i class="fas fa-fw fa-trash"></i></a>
                                    @include('cms.profession.modal.delete', ['profession' => $data])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection