@extends('cms.layouts.main', ['title' => 'Detail Pembantu'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <h1 class="h3 mb-4 text-gray-800">Detail Pembantu</h1>
        <div class="d-flex">
            @hasrole('superadmin|admin')
                <a href="{{ route('users-servant.edit', $user->id) }}" class="btn btn-warning mr-1"><i
                        class="fas fa-fw fa-user-edit"></i></a>
            @endhasrole
            <a href="{{ route('users-servant.index') }}" class="btn btn-secondary"><i class="fas fa-fw fa-arrow-left"></i></a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 mb-3 mb-lg-0">
            <div class="card shadow mb-3 p-3">
                @if ($user->servantDetails->photo)
                    <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $user->servantDetails->photo]) }}"
                        class="img-fluid rounded mx-auto d-block zoomable-image" style="max-height: 150px;" alt="...">
                @else
                    <img src="{{ asset('assets/img/undraw_rocket.svg') }}"
                        class="img-fluid rounded mx-auto d-block zoomable-image" style="max-height: 150px;" alt="...">
                @endif

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
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h5 font-weight-bold">Keahlian</h1>
                        @hasrole('superadmin|admin')
                            <a class="btn btn-primary mb-2 mb-lg-0" href="#" data-toggle="modal"
                                data-target="#createSkillModal-{{ $user->id }}">
                                <i class="fas fa-plus"></i>
                            </a>
                            @include('cms.user.partials.servant.skill.create', [
                                'user' => $user,
                            ])
                        @endhasrole
                    </div>
                </div>
                <div class="card-body">
                    <ul>
                        @if ($user->servantSkills->count() > 0)
                            @foreach ($user->servantSkills as $dataSkill)
                                <li>
                                    <a class="text-capitalize" href="#" data-toggle="modal"
                                        data-target="#updateSkillModal-{{ $dataSkill->id }}">
                                        {{ $dataSkill->skill }} ({{ $dataSkill->level }})
                                    </a>
                                    @include('cms.user.partials.servant.skill.edit', [
                                        'user' => $user,
                                    ])
                                </li>
                            @endforeach
                        @else
                            <li>
                                Belum Ada Keahlian
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-3">
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
                                <th scope="row">Rekening</th>
                                <td>:</td>
                                <td>
                                    @if ($user->servantDetails->is_bank == 1)
                                        {{ $user->servantDetails->account_number }} ({{ $user->servantDetails->bank_name }})
                                    @else
                                        Belum memiliki rekening
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">BPJS</th>
                                <td>:</td>
                                <td>
                                    @if ($user->servantDetails->is_bpjs == 1)
                                        {{ $user->servantDetails->number_bpjs }} ({{ $user->servantDetails->type_bpjs }})
                                    @else
                                        Belum memiliki BPJS
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Nomor Telepon</th>
                                <td>:</td>
                                <td>{{ $user->servantDetails->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Nomor Darurat</th>
                                <td>:</td>
                                <td>{{ $user->servantDetails->emergency_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Alamat</th>
                                <td>:</td>
                                <td>{{ $user->servantDetails->address }} RT {{ $user->servantDetails->rt }} RW {{ $user->servantDetails->rw }}, {{ $user->servantDetails->village }}, {{ $user->servantDetails->district }}, {{ $user->servantDetails->regency }}, {{ $user->servantDetails->province }}</td>
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

            <div class="card shadow">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h5 font-weight-bold">Berkas Kelengkapan</h1>
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
                        @endhasrole
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-responsive table-borderless">
                        <tbody>
                            <tr>
                                <th scope="row">KTP</th>
                                <td>:</td>
                                <td>
                                    @if ($user->servantDetails->identity_card == null)
                                        -
                                    @else
                                        <img src="{{ route('getImage', ['path' => 'identity_card', 'imageName' => $user->servantDetails->identity_card]) }}"
                                            alt="Kartu Tanda Penduduk" class="img-fluid rounded zoomable-image"
                                            style="max-height: 150px;">
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Kartu Keluarga</th>
                                <td>:</td>
                                <td>
                                    @if ($user->servantDetails->family_card == null)
                                        -
                                    @else
                                        <img src="{{ route('getImage', ['path' => 'family_card', 'imageName' => $user->servantDetails->family_card]) }}"
                                            alt="Kartu Tanda Penduduk" class="img-fluid rounded zoomable-image"
                                            style="max-height: 150px;">
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
