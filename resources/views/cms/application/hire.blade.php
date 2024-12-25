@extends('cms.layouts.main', ['title' => 'Lamaran Kerja'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Lamaran - Hire</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Majikan</th>
                            @hasrole('superadmin|admin')
                                <th>Nama Pelamar</th>
                            @endhasrole
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->employe->name }}</td>
                                @hasrole('superadmin|admin')
                                    <td>{{ $data->servant->name }}</td>
                                @endhasrole
                                <td class="text-center">
                                    <span
                                        class="p-2 badge badge-{{ $data->status == 'accepted' ? 'success' : 'danger' }}">{{ $data->status == 'accepted' ? 'Diterima' : 'Ditolak' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
