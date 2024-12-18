@extends('cms.layouts.main', ['title' => 'Users Majikan'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Users Majikan</h1>
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
            @include('cms.user.partials.employe.create-employe')
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Alamat</th>
                            <th>No Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->employeDetails->address ?? 'Belum diisi' }}</td>
                                <td>{{ $user->employeDetails->phone == '-' ? 'Belum diisi' : $user->employeDetails->phone  ?? 'Belum diisi' }}</td>
                                <td>
                                    <a class="btn btn-warning" href="#" data-toggle="modal"
                                        data-target="#editModal-{{ $user->id }}">Edit</a>
                                    @include('cms.user.partials.employe.edit-employe', ['user' => $user])

                                    <a class="btn btn-danger" href="#" data-toggle="modal"
                                        data-target="#deleteModal-{{ $user->id }}">Hapus</a>
                                    @include('cms.user.partials.employe.delete-employe', ['user' => $user])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
