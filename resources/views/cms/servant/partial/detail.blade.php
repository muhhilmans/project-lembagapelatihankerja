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
                            <tr>
                                <th scope="row">Inval</th>
                                <td>:</td>
                                <td>
                                    <span
                                        class="p-2 badge badge-{{ $data->servantDetails->is_inval == 1 ? 'success' : 'danger' }}">{{ $data->servantDetails->is_inval == 1 ? 'Bersedia' : 'Tidak' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Pulang Pergi</th>
                                <td>:</td>
                                <td>
                                    <span
                                        class="p-2 badge badge-{{ $data->servantDetails->is_stay == 1 ? 'success' : 'danger' }}">{{ $data->servantDetails->is_stay == 1 ? 'Bersedia' : 'Tidak' }}</span>
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
                            @if ($data->servantDetails->marital_status != 'single')
                                <tr>
                                    <th scope="row">Anak</th>
                                    <td>:</td>
                                    <td>{{ $data->servantDetails->children == 0 ? 'Tidak Ada' : 'Ada' }}</td>
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
                            @hasrole('superadmin|admin|owner')
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
                            @endhasrole
                            @hasrole('majikan')
                            <tr>
                                <th scope="row">Asal Kota</th>
                                <td>:</td>
                                <td>{{ $data->servantDetails->regency }},
                                    {{ $data->servantDetails->province }}</td>
                            </tr>
                            @endhasrole
                            <tr>
                                <th scope="row">Pengalaman Kerja</th>
                                <td>:</td>
                                <td>{{ $data->servantDetails->experience }} Tahun</td>
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

    {{-- ULASAN & RATING SECTION --}}
    <style>
        .review-card {
            border-radius: 12px;
            border: 1px solid #e3e6f0;
            background-color: #f8f9fc;
            padding: 1rem;
            height: 100%;
            transition: all 0.3s ease;
        }
        .review-card:hover {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transform: translateY(-2px);
        }
        .review-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4e73df;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-star fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold text-dark mb-0">Ulasan & Rating Pembantu</h5>
                            <div class="text-warning mt-1">
                                <span class="font-weight-bold" style="font-size: 1.2rem;">{{ $data->average_rating > 0 ? $data->average_rating : '0.0' }}</span>
                                <small class="text-muted ml-1">/ 5 ({{ $data->review_count }} Ulasan)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        @if($data->receivedReviews->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-comment-slash fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted">Belum ada ulasan untuk pembantu ini.</p>
                            </div>
                        @else
                            <div class="row">
                                @foreach($data->receivedReviews as $review)
                                    <div class="col-md-6 mb-3">
                                        <div class="review-card">
                                            <div class="d-flex justify-content-between mb-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="review-avatar mr-2 shadow-sm">
                                                        {{ strtoupper(substr($review->reviewer->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 font-weight-bold text-dark">{{ $review->reviewer->name }}</h6>
                                                        <small class="text-muted"><i class="fas fa-user-tag mr-1"></i>{{ ucfirst($review->reviewer->roles->first()->name ?? 'User') }}</small>
                                                    </div>
                                                </div>
                                                <small class="text-muted text-right">{{ $review->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="mb-0 text-dark font-italic">"{{ $review->comment }}"</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
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
