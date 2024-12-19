@extends('cms.layouts.main', ['title' => 'Detail Pembantu'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <h1 class="h3 mb-4 text-gray-800">Detail Pembantu</h1>
        <a href="{{ route('users-servant.index') }}" class="btn btn-secondary"><i class="fas fa-fw fa-arrow-left"></i></a>
    </div>
    @if (session('error'))
        <h5 class="text-danger">{{ session('error') }}</h5>
    @endif

    @if (session('success'))
        <h5 class="text-success">{{ session('success') }}</h5>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-3 p-3">
                <img src="{{ asset('assets/img/undraw_profile_1.svg') }}" class="img-fluid rounded mx-auto d-block"
                    style="max-height: 150px;" alt="...">

                <div class="card-body">
                    <table class="table table-responsive table-borderless">
                        <tbody>
                            <tr>
                                <th scope="row">Nama</th>
                                <td>:</td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Username</th>
                                <td>:</td>
                                <td>{{ $user->username }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Email</th>
                                <td>:</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Akun</th>
                                <td>:</td>
                                <td><span
                                        class="p-2 badge badge-{{ $user->is_active == 1 ? 'success' : 'danger' }}">{{ $user->is_active == 1 ? 'Aktif' : 'Tidak Aktif' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header">
                    <h1 class="h5 font-weight-bold">Keahlian</h1>
                </div>
                <div class="card-body">
                    <ul>
                        <li>memasak</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h1 class="h5 font-weight-bold">Detail Informasi</h1>
                </div>
                <div class="card-body">
                    <table class="table table-responsive table-borderless">
                        <tbody>
                            <tr>
                                <th scope="row">TTL</th>
                                <td>:</td>
                                <td>{{ $user->servantDetails->place_of_birth }},
                                    {{ $user->servantDetails->date_of_birth }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Jenis Kelamin</th>
                                <td>:</td>
                                <td>
                                    @if ($user->servantDetails->gender == 'male')
                                        Laki-laki
                                    @elseif ($user->servantDetails->gender == 'female')
                                        Perempuan
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Agama</th>
                                <td>:</td>
                                <td>{{ $user->servantDetails->religion }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Status</th>
                                <td>:</td>
                                <td>
                                    @if ($user->servantDetails->marital_status == 'married')
                                        Menikah
                                    @elseif ($user->servantDetails->marital_status == 'single')
                                        Lajang
                                    @elseif ($user->servantDetails->marital_status == 'divorced')
                                        Cerai
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @if ($user->servantDetails->children > 0)
                                <tr>
                                    <th scope="row">Anak</th>
                                    <td>:</td>
                                    <td>{{ $user->servantDetails->children }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th scope="row">Profesi</th>
                                <td>:</td>
                                <td>{{ $user->servantDetails->profession->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Pendidikan Terakhir</th>
                                <td>:</td>
                                <td>
                                    @if ($user->servantDetails->last_education == 'not_filled')
                                        -
                                    @else
                                    {{ $user->servantDetails->last_education }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Alamat</th>
                                <td>:</td>
                                <td>{{ $user->servantDetails->address }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Status Kerja</th>
                                <td>:</td>
                                <td>{{ $user->servantDetails->working_status == 1 ? 'Bekerja' : 'Tidak Bekerja' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Pengalaman Kerja</th>
                                <td>:</td>
                                <td>{{ $user->servantDetails->experience }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Deskripsi</th>
                                <td>:</td>
                                <td>{{ $user->servantDetails->description }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
