@extends('cms.layouts.main', ['title' => 'Detail Profil'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <h1 class="h3 mb-4 text-gray-800">Detail Profil</h1>
        <a href="{{ route('users-employe.index') }}" class="btn btn-secondary"><i class="fas fa-fw fa-arrow-left"></i></a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <table class="table table-responsive table-borderless">
                <tbody>
                    <tr>
                        <th scope="row">Nama</th>
                        <td>:</td>
                        <td>{{ $data->name }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Username</th>
                        <td>:</td>
                        <td>{{ $data->username }}
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Email</th>
                        <td>:</td>
                        <td>{{ $data->email }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Nomor Telepon</th>
                        <td>:</td>
                        <td>{{ $data->employeDetails->phone }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Alamat</th>
                        <td>:</td>
                        <td>{{ $data->employeDetails->address }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Rekening</th>
                        <td>:</td>
                        <td>{{ $data->employeDetails->account_number }} ({{ $data->employeDetails->bank_name }})</td>
                    </tr>
                    <tr>
                        <th scope="row">KTP</th>
                        <td>:</td>
                        <td>
                            @if ($data->employeDetails->identity_card == null)
                                -
                            @else
                                <img src="{{ route('getImage', ['path' => 'identity_card', 'imageName' => $data->employeDetails->identity_card]) }}"
                                    alt="Kartu Tanda Penduduk" class="img-fluid rounded zoomable-image"
                                    style="max-height: 150px;">
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
