@extends('cms.layouts.main', ['title' => 'Users Pembantu'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Users Pembantu</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        @hasrole('superadmin|admin')
            <div class="card-header py-3">
                <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#createModal">
                    Tambah
                </a>
                @include('cms.user.partials.servant.create-servant')
            </div>
        @endhasrole

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Status</th>
                            @hasrole('superadmin|admin|owner')
                                <th>Aksi</th>
                            @endhasrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->username }}</td>
                                </td>
                                <td class="text-center"><span
                                        class="p-2 badge badge-{{ $user->is_active == 1 ? 'success' : 'danger' }}">{{ $user->is_active == 1 ? 'Aktif' : 'Tidak Aktif' }}</span>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-info mb-2 mb-lg-0"
                                        href="{{ route('users-servant.show', $user->id) }}"><i
                                            class="fas fa-fw fa-eye"></i></a>

                                    @hasrole('superadmin|admin')
                                        <a class="btn btn-warning mb-2 mb-lg-0" href="#" data-toggle="modal"
                                            data-target="#changeModal-{{ $user->id }}">
                                            @if ($user->is_active == 1)
                                                <i class="fas fa-fw fa-toggle-off"></i>
                                            @else
                                                <i class="fas fa-fw fa-toggle-on"></i>
                                            @endif
                                        </a>
                                        @include('cms.user.partials.servant.change-servant', [
                                            'user' => $user,
                                        ])

                                        <a class="btn btn-danger" href="#" data-toggle="modal"
                                            data-target="#deleteModal-{{ $user->id }}"><i
                                                class="fas fa-fw fa-trash"></i></a>
                                        @include('cms.user.partials.servant.delete-servant', [
                                            'user' => $user,
                                        ])
                                    @endhasrole
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
