@extends('cms.layouts.main', ['title' => 'Detail Pembantu'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <h1 class="h3 mb-4 text-gray-800">Detail Pembantu</h1>
        <div class="d-flex">
            @php
                $applicationExists = \App\Models\Application::where('servant_id', $data->id)
                    ->where('employe_id', auth()->user()->id)
                    ->whereIn('status', ['interview', 'verify', 'passed', 'choose', 'accepted', 'rejected', 'pending'])
                    ->exists();
            @endphp

            @if (!$applicationExists)
                <a href="#" class="btn btn-primary mr-1" data-toggle="modal"
                    data-target="#hireModal-{{ $data->id }}">Hire</a>
                @include('cms.servant.modal.hire')
            @endif

            <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="fas fa-fw fa-arrow-left"></i></a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 mb-3 mb-lg-0">
            <div class="card shadow mb-3 p-3">
                @if ($data->servantDetails->photo)
                    <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $data->servantDetails->photo]) }}"
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
                                <td>{{ $data->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">username</th>
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
                                <th scope="row">Status</th>
                                <td>:</td>
                                <td>
                                    <span
                                        class="p-2 badge badge-{{ $data->servantDetails->working_status == 1 ? 'success' : 'danger' }}">{{ $data->servantDetails->working_status == 1 ? 'Bekerja' : 'Tidak Bekerja' }}</span>
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
                    </div>
                </div>
                <div class="card-body">
                    <ul>
                        @if ($data->servantSkills->count() > 0)
                            @foreach ($data->servantSkills as $dataSkill)
                                <li>
                                    {{ $dataSkill->skill }} ({{ $dataSkill->level }})
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
                                <td>{{ $data->servantDetails->place_of_birth }},
                                    {{ \Carbon\Carbon::parse($data->servantDetails->date_of_birth)->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Jenis Kelamin</th>
                                <td>:</td>
                                <td>
                                    @if ($data->servantDetails->gender == 'male')
                                        Laki-laki
                                    @elseif ($data->servantDetails->gender == 'female')
                                        Perempuan
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Agama</th>
                                <td>:</td>
                                <td>{{ $data->servantDetails->religion }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Status</th>
                                <td>:</td>
                                <td>
                                    @if ($data->servantDetails->marital_status == 'married')
                                        Menikah
                                    @elseif ($data->servantDetails->marital_status == 'single')
                                        Lajang
                                    @elseif ($data->servantDetails->marital_status == 'divorced')
                                        Cerai
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @if ($data->servantDetails->children > 0)
                                <tr>
                                    <th scope="row">Anak</th>
                                    <td>:</td>
                                    <td>{{ $data->servantDetails->children }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th scope="row">Profesi</th>
                                <td>:</td>
                                <td>{{ $data->servantDetails->profession->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Pendidikan Terakhir</th>
                                <td>:</td>
                                <td>
                                    @if ($data->servantDetails->last_education == 'not_filled')
                                        -
                                    @else
                                        {{ $data->servantDetails->last_education }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Nomor Telepon</th>
                                <td>:</td>
                                <td>{{ $data->servantDetails->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Nomor Darurat</th>
                                <td>:</td>
                                <td>{{ $data->servantDetails->emergency_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Alamat</th>
                                <td>:</td>
                                <td>{{ $data->servantDetails->address }} RT {{ $data->servantDetails->rt }} RW
                                    {{ $data->servantDetails->rw }}, {{ $data->servantDetails->village }},
                                    {{ $data->servantDetails->district }}, {{ $data->servantDetails->regency }},
                                    {{ $data->servantDetails->province }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Pengalaman Kerja</th>
                                <td>:</td>
                                <td>{{ $data->servantDetails->experience }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Deskripsi</th>
                                <td>:</td>
                                <td>{{ $data->servantDetails->description }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/summernote/summernote-bs4.min.css') }}">
@endpush

@push('custom-script')
    <script src="{{ asset('assets/vendor/summernote/summernote-bs4.min.js') }}"></script>
@endpush
