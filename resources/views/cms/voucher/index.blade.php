@extends('cms.layouts.main', ['title' => 'Voucher'])

@section('content')
    <!-- Page Heading -->
    <div class="mb-4 d-flex justify-content-between align-items-baseline">
        <h1 class="h3 text-gray-800">Kelola Voucher</h1>
        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus"></i>
            Tambah</a>
        @include('cms.voucher.modal.create')
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Kode</th>
                            <th>Max Pengguna</th>
                            <th>Max Penggunaan</th>
                            <th>Masa Berlaku</th>
                            <th>Diskon</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->code }}</td>
                                <td class="text-center">
                                    @if ($data->people_used != null)
                                        {{ $data->people_used }} Orang
                                    @else
                                        Tidak Ada Batasan
                                    @endif
                                </td>
                                <td class="text-center">{{ $data->time_used }} Kali</td>
                                <td class="text-center">
                                    {{ $data->expired_date ? \Carbon\Carbon::parse($data->expired_date)->format('d-M-Y') : 'Tidak Ada Masa Berlaku' }}
                                </td>
                                <td class="text-center">{{ $data->discount }} %</td>
                                <td class="text-center"><span
                                        class="p-2 badge badge-{{ $data->is_active == 1 ? 'success' : 'danger' }}">{{ $data->is_active == 1 ? 'Aktif' : 'Tidak Aktif' }}</span>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-info mb-1 mb-lg-0" href="#" data-toggle="modal"
                                        data-target="#changeModal-{{ $data->id }}">
                                        @if ($data->is_active == 1)
                                            <i class="fas fa-fw fa-toggle-off"></i>
                                        @else
                                            <i class="fas fa-fw fa-toggle-on"></i>
                                        @endif
                                    </a>
                                    @include('cms.voucher.modal.change', ['voucher' => $data])

                                    <a class="btn btn-sm btn-warning mb-1 mb-lg-0" href="#" data-toggle="modal"
                                        data-target="#editModal-{{ $data->id }}"><i class="fas fa-fw fa-edit"></i></a>
                                    @include('cms.voucher.modal.edit', ['voucher' => $data])

                                    <a class="btn btn-sm btn-danger" href="#" data-toggle="modal"
                                        data-target="#deleteModal-{{ $data->id }}"><i
                                            class="fas fa-fw fa-trash"></i></a>
                                    @include('cms.voucher.modal.delete', ['voucher' => $data])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
