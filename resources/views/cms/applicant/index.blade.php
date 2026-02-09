@extends('cms.layouts.main', ['title' => 'Pelamar'])

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pelamar</h1>
        
        <!-- Filter Dropdown -->
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-filter mr-1"></i>
                @if($type == 'hire')
                    Hire
                @elseif($type == 'mandiri')
                    Mandiri
                @else
                    Semua
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="filterDropdown">
                <a class="dropdown-item {{ $type == 'all' ? 'active' : '' }}" href="{{ route('applicant.index', ['type' => 'all']) }}">
                    <i class="fas fa-list mr-2"></i>Semua
                </a>
                <a class="dropdown-item {{ $type == 'hire' ? 'active' : '' }}" href="{{ route('applicant.index', ['type' => 'hire']) }}">
                    <i class="fas fa-handshake mr-2"></i>Hire
                </a>
                <a class="dropdown-item {{ $type == 'mandiri' ? 'active' : '' }}" href="{{ route('applicant.index', ['type' => 'mandiri']) }}">
                    <i class="fas fa-user mr-2"></i>Mandiri
                </a>
            </div>
        </div>
    </div>

    @if($type == 'all' || $type == 'hire')
        @if($hireData->isNotEmpty())
            <!-- Section Hire -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-handshake mr-2"></i>Pelamar - Hire
                    </h6>
                    <span class="badge badge-primary">{{ $hireData->count() }} Pelamar</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($hireData as $d)
                            <div class="col-lg-3 mb-4">
                                <div class="card shadow-sm h-100">
                                    <!-- Photo -->
                                    @if (isset($d->servant->servantDetails) && $d->servant->servantDetails->photo)
                                        <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $d->servant->servantDetails->photo]) }}"
                                            class="card-img-top img-fluid" alt="Pembantu {{ $d->servant->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('assets/img/undraw_rocket.svg') }}" class="card-img-top img-fluid p-3"
                                            alt="Pembantu {{ $d->servant->name }}" style="height: 200px; object-fit: contain;">
                                    @endif

                                    <!-- Card Content -->
                                    <div class="card-body">
                                        <span class="badge badge-info mb-2">Hire</span>
                                        <ul class="list-unstyled mb-3">
                                            @hasrole('superadmin|admin')
                                                <li class="mb-1"><strong>Dihire oleh:</strong> {{ $d->employe->name }}</li>
                                                @if ($d->status == 'schedule')
                                                    <li class="mb-1"><strong>Tanggal Interview:</strong>
                                                        {{ \Carbon\Carbon::parse($d->interview_date)->format('d-m-Y') }}</li>
                                                    <li><strong>Catatan:</strong> {!! $d->notes_interview !!}</li>
                                                    <li class="mb-1"><strong>No Majikan:</strong> {{ $d->employe->employeDetails->phone }}
                                                    </li>
                                                    <li class="mb-1"><strong>No Pembantu:</strong>
                                                        {{ $d->servant->servantDetails->phone }}</li>
                                                    <li class="mb-1"><strong>No Darurat Pembantu:</strong>
                                                        {{ $d->servant->servantDetails->emergency_number }}</li>
                                                @endif
                                            @endhasrole
                                            @if ($d->status == 'interview')
                                                <li class="mb-1"><strong>Link Interview:</strong> <a href="{{ $d->link_interview }}"
                                                        target="_blank" rel="noopener noreferrer">{{ $d->link_interview }}</a></li>
                                                <li class="mb-1"><strong>Tanggal Interview:</strong>
                                                    {{ \Carbon\Carbon::parse($d->interview_date)->format('d-m-Y') }}</li>
                                                <li class="mb-1"><strong>Catatan:</strong> {!! $d->notes_interview !!}</li>
                                            @endif
                                            @if ($d->salary != null)
                                                <li><strong>Gaji:</strong> Rp. {{ number_format($d->salary, 0, ',', '.') }}</li>
                                            @endif
                                        </ul>

                                        <ul class="list-unstyled mb-3">
                                            <li class="mb-2">
                                                <i class="fas fa-user"></i>
                                                <strong>Nama:</strong> {{ $d->servant->name }}
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-calendar-alt"></i>
                                                <strong>Usia:</strong>
                                                @if (optional($d->servant->servantDetails)->date_of_birth)
                                                    {{ \Carbon\Carbon::parse($d->servant->servantDetails->date_of_birth)->age }}
                                                    Tahun
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-praying-hands"></i>
                                                <strong>Agama:</strong>
                                                @if (optional($d->servant->servantDetails)->religion)
                                                    {{ $d->servant->servantDetails->religion }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-user-tie"></i>
                                                <strong>Profesi:</strong>
                                                @if (optional($d->servant->servantDetails)->profession && optional($d->servant->servantDetails->profession)->name)
                                                    {{ $d->servant->servantDetails->profession->name }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-briefcase"></i>
                                                <strong>Pengalaman:</strong>
                                                @if (optional($d->servant->servantDetails)->experience)
                                                    {{ $d->servant->servantDetails->experience }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-cogs"></i>
                                                <strong>Inval:</strong>
                                                @if ($d->servant->servantDetails->is_inval)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </li>
                                            <li>
                                                <i class="fas fa-home"></i>
                                                <strong>Pulang Pergi:</strong>
                                                @if ($d->servant->servantDetails->is_stay)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </li>
                                        </ul>
                                        <p class="card-text text-muted">
                                            {{ \Illuminate\Support\Str::limit(optional($d->servant->servantDetails)->description ?? 'Belum ada deskripsi', 100, '...') }}
                                        </p>
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="card-footer">
                                        @include('cms.applicant.partial.hire-footer', ['d' => $d])
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif($type == 'hire')
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Belum ada pelamar Hire</p>
                </div>
            </div>
        @endif
    @endif

    @if($type == 'all' || $type == 'mandiri')
        @if($indieData->isNotEmpty())
            <!-- Section Mandiri -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-user mr-2"></i>Pelamar - Mandiri
                    </h6>
                    <span class="badge badge-success">{{ $indieData->count() }} Pelamar</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($indieData as $d)
                            <div class="col-lg-3 mb-4">
                                <div class="card shadow-sm h-100">
                                    <!-- Photo -->
                                    @if (isset($d->servant->servantDetails) && $d->servant->servantDetails->photo)
                                        <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $d->servant->servantDetails->photo]) }}"
                                            class="card-img-top img-fluid" alt="Pembantu {{ $d->servant->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('assets/img/undraw_rocket.svg') }}" class="card-img-top img-fluid p-3"
                                            alt="Pembantu {{ $d->servant->name }}" style="height: 200px; object-fit: contain;">
                                    @endif

                                    <!-- Card Content -->
                                    <div class="card-body">
                                        <span class="badge badge-success mb-2">Mandiri</span>
                                        <ul class="list-unstyled mb-3">
                                            <li class="mb-1"><strong>Lowongan Pekerjaan:</strong> {{ $d->vacancy->title }}</li>
                                            @hasrole('superadmin|admin')
                                                @if ($d->status == 'schedule')
                                                    <li class="mb-1"><strong>Tanggal Interview:</strong>
                                                        {{ \Carbon\Carbon::parse($d->interview_date)->format('d-m-Y') }}</li>
                                                    <li><strong>Catatan:</strong> {!! $d->notes_interview !!}</li>
                                                    <li class="mb-1"><strong>No Majikan:</strong>
                                                        {{ $d->vacancy->user->employeDetails->phone }}</li>
                                                    <li class="mb-1"><strong>No Pembantu:</strong>
                                                        {{ $d->servant->servantDetails->phone }}</li>
                                                    <li class="mb-1"><strong>No Darurat Pembantu:</strong>
                                                        {{ $d->servant->servantDetails->emergency_number }}</li>
                                                @endif
                                            @endhasrole
                                            @if ($d->status == 'interview')
                                                <li class="mb-1"><strong>Link Interview:</strong> <a href="{{ $d->link_interview }}"
                                                        target="_blank" rel="noopener noreferrer">{{ $d->link_interview }}</a></li>
                                                <li class="mb-1"><strong>Tanggal Interview:</strong>
                                                    {{ \Carbon\Carbon::parse($d->interview_date)->format('d-m-Y') }}</li>
                                                <li class="mb-1"><strong>Catatan:</strong> {!! $d->notes_interview !!}</li>
                                            @endif
                                            @if ($d->salary != null)
                                                <li><strong>Gaji:</strong> Rp. {{ number_format($d->salary, 0, ',', '.') }}</li>
                                            @endif
                                        </ul>

                                        <ul class="list-unstyled mb-3">
                                            <li class="mb-2">
                                                <i class="fas fa-user"></i>
                                                <strong>Nama:</strong> {{ $d->servant->name }}
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-calendar-alt"></i>
                                                <strong>Usia:</strong>
                                                @if (optional($d->servant->servantDetails)->date_of_birth)
                                                    {{ \Carbon\Carbon::parse($d->servant->servantDetails->date_of_birth)->age }}
                                                    Tahun
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-praying-hands"></i>
                                                <strong>Agama:</strong>
                                                @if (optional($d->servant->servantDetails)->religion)
                                                    {{ $d->servant->servantDetails->religion }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-user-tie"></i>
                                                <strong>Profesi:</strong>
                                                @if (optional($d->servant->servantDetails)->profession && optional($d->servant->servantDetails->profession)->name)
                                                    {{ $d->servant->servantDetails->profession->name }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-briefcase"></i>
                                                <strong>Pengalaman:</strong>
                                                @if (optional($d->servant->servantDetails)->experience)
                                                    {{ $d->servant->servantDetails->experience }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-cogs"></i>
                                                <strong>Inval:</strong>
                                                @if ($d->servant->servantDetails->is_inval)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </li>
                                            <li>
                                                <i class="fas fa-home"></i>
                                                <strong>Pulang Pergi:</strong>
                                                @if ($d->servant->servantDetails->is_stay)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </li>
                                        </ul>
                                        <p class="card-text text-muted">
                                            {{ \Illuminate\Support\Str::limit(optional($d->servant->servantDetails)->description ?? 'Belum ada deskripsi', 100, '...') }}
                                        </p>
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="card-footer text-right">
                                        @include('cms.applicant.partial.indie-footer', ['d' => $d])
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif($type == 'mandiri')
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Belum ada pelamar Mandiri</p>
                </div>
            </div>
        @endif
    @endif

    @if($hireData->isEmpty() && $indieData->isEmpty() && $type == 'all')
        <div class="card shadow mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted">Belum ada pelamar</p>
            </div>
        </div>
    @endif
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/summernote/summernote-bs4.min.css') }}">
@endpush

@push('custom-script')
    <script src="{{ asset('assets/vendor/summernote/summernote-bs4.min.js') }}"></script>
@endpush
