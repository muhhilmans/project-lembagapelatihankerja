@extends('cms.layouts.main', ['title' => 'Detail Lowongan'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <h1 class="h3 mb-4 text-gray-800">Detail Lowongan</h1>
        <a href="{{ route('vacancies.index') }}" class="btn btn-secondary"><i class="fas fa-fw fa-arrow-left"></i></a>
    </div>
    @if (session('error'))
        <h5 class="text-danger">{{ session('error') }}</h5>
    @endif

    @if (session('success'))
        <h5 class="text-success">{{ session('success') }}</h5>
    @endif

    <div class="card shadow">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="vacancyTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="detail-tab" data-toggle="tab" href="#detail" role="tab"
                        aria-controls="detail" aria-selected="true">Detail</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="applicant-tab" data-toggle="tab" href="#applicant" role="tab"
                        aria-controls="applicant" aria-selected="false">Pelamar</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="vacancyTabContent">
                <!-- Tab: Detail -->
                <div class="tab-pane fade show active" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                    <h5 class="card-title"><strong>{{ $data->title }}</strong></h5>
                    <p class="card-text"><strong>Batas Lamaran:</strong> {{ $data->closing_date }}</p>
                    <p class="card-text"><strong>Deskripsi:</strong> {!! $data->description !!}</p>
                    <p class="card-text"><strong>Spesifikasi:</strong> {!! $data->requirements !!}</p>
                    @if ($data->benefits != null)
                        <p class="card-text"><strong>Keuntungan:</strong> {!! $data->benefits !!}</p>
                    @endif
                </div>

                <!-- Tab: Link -->
                <div class="tab-pane fade" id="applicant" role="tabpanel" aria-labelledby="applicant-tab">
                    <div class="row row-cols-1 row-cols-md-4 g-3">
                        @foreach ($data->applyJobs as $d)
                            <div class="col-lg-3 mb-3 mb-lg-0">
                                <div class="card shadow-sm">
                                    <!-- Photo -->
                                    @if (isset($d->servant->servantDetails) && $d->servant->servantDetails->photo)
                                        <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $d->servant->servantDetails->photo]) }}"
                                            class="card-img-top img-fluid" style="max-height: 150px;"
                                            alt="Pembantu {{ $d->servant->name }}">
                                    @else
                                        <img src="{{ asset('assets/img/undraw_rocket.svg') }}"
                                            class="card-img-top img-fluid p-3" style="max-height: 150px;"
                                            alt="Pembantu {{ $d->servant->name }}">
                                    @endif

                                    <!-- Card Content -->
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-3">
                                            <li class="mb-2">
                                                <i class="fas fa-user"></i>
                                                <strong>Nama:</strong> {{ $d->servant->name }}
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-calendar-alt"></i>
                                                <strong>Usia:</strong>
                                                {{ \Carbon\Carbon::parse($d->servant->servantDetails->date_of_birth)->age }}
                                                Tahun
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-praying-hands"></i>
                                                <strong>Agama:</strong> {{ $d->servant->servantDetails->religion }}
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-user-tie"></i>
                                                <strong>Profesi:</strong>
                                                {{ $d->servant->servantDetails->profession->name }}
                                            </li>
                                            <li>
                                                <i class="fas fa-briefcase"></i>
                                                <strong>Pengalaman:</strong> {{ $d->servant->servantDetails->experience }}
                                            </li>
                                        </ul>
                                        <p class="card-text text-muted">
                                            {{ $d->servant->servantDetails->description == '-' ? 'Belum ada deskripsi' : \Illuminate\Support\Str::limit($d->servant->servantDetails->description, 100, '...') }}
                                        </p>
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="card-footer text-right">
                                        @if ($d->status == 'pending')
                                            <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                                data-target="#acceptModal-{{ $d->id }}">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            @include('cms.vacancy.modal.accept', ['data' => $d])

                                            <a href="#" class="btn btn-sm btn-danger" data-toggle="modal"
                                                data-target="#rejectModal-{{ $d->id }}">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            @include('cms.vacancy.modal.reject', ['data' => $d])
                                        @endif

                                        @if ($d->status == 'interview')
                                            <a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#contractModal-{{ $d->id }}">
                                                <i class="fas fa-file-contract"></i>
                                            </a>
                                            @include('cms.vacancy.modal.contract', ['data' => $d])

                                            <a href="#" class="btn btn-sm btn-danger" data-toggle="modal"
                                                data-target="#rejectModal-{{ $d->id }}">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            @include('cms.vacancy.modal.reject', ['data' => $d])
                                        @endif

                                        @if ($d->status == 'accepted')
                                            <a href="{{ route('contract.download', $d->id) }}" class="btn btn-sm btn-success"><i class="fas fa-file-download"></i></a>
                                        @endif

                                        <a class="btn btn-sm btn-info" href="{{ route('show-servant', $d->servant->id) }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
