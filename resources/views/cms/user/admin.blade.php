@extends('cms.layouts.main', ['title' => 'Users Admin'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Users Admin</h1>
    @if (session('error'))
        <h5 class="text-danger">{{ session('error') }}</h5>
    @endif

    @if (session('success'))
        <h5 class="text-success">{{ session('success') }}</h5>
    @endif

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#createModal">
                Tambah
            </a>
            @include('cms.user.partials.admin.create-admin')
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->username }}</td>
                                <td class="text-center">
                                    <a class="btn btn-warning mb-2 mb-lg-0" href="#" data-toggle="modal"
                                        data-target="#editModal-{{ $user->id }}"><i class="fas fa-fw fa-user-edit"></i></a>
                                    @include('cms.user.partials.admin.edit-admin', ['user' => $user])

                                    <a class="btn btn-danger" href="#" data-toggle="modal"
                                        data-target="#deleteModal-{{ $user->id }}"><i class="fas fa-fw fa-trash"></i></a>
                                    @include('cms.user.partials.admin.delete-admin', ['user' => $user])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
