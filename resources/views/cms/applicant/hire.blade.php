@extends('cms.layouts.main', ['title' => 'Pelamar'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Pelamar - Hire</h1>

    <div class="row">
        @if ($datas->isEmpty())
            <div class="col-12 text-center">
                <p class="text-muted text-center">Belum ada pelamar</p>
            </div>
        @else
            @foreach ($datas as $d)
                <div class="col-lg-3 mb-3 mb-lg-0">
                    <div class="card shadow-sm">
                        <!-- Photo -->
                        @if (isset($d->servant->servantDetails) && $d->servant->servantDetails->photo)
                            <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $d->servant->servantDetails->photo]) }}"
                                class="card-img-top img-fluid" style="max-height: 150px;"
                                alt="Pembantu {{ $d->servant->name }}">
                        @else
                            <img src="{{ asset('assets/img/undraw_rocket.svg') }}" class="card-img-top img-fluid p-3"
                                style="max-height: 150px;" alt="Pembantu {{ $d->servant->name }}">
                        @endif

                        <!-- Card Content -->
                        <div class="card-body">
                            @hasrole('superadmin|admin')
                                <p class="card-text"><strong>Dihire oleh:</strong> {{ $d->employe->name }}</p>
                            @endhasrole

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
                                <li>
                                    <i class="fas fa-briefcase"></i>
                                    <strong>Pengalaman:</strong>
                                    @if (optional($d->servant->servantDetails)->experience)
                                        {{ $d->servant->servantDetails->experience }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </li>
                            </ul>
                            <p class="card-text text-muted">
                                {{ \Illuminate\Support\Str::limit(optional($d->servant->servantDetails)->description ?? 'Belum ada deskripsi', 100, '...') }}
                            </p>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <span
                                    class="badge badge-{{ match ($d->status) {
                                        'interview' => 'warning',
                                        'rejected' => 'danger',
                                        'accepted' => 'success',
                                        default => 'secondary',
                                    } }} p-2">{{ $d->status }}
                                </span>

                                <div class="row">
                                    @if ($d->status == 'interview')
                                        <a href="#" class="btn btn-sm btn-primary mr-1" data-toggle="modal"
                                            data-target="#hireContractModal-{{ $d->id }}">
                                            <i class="fas fa-file-contract"></i>
                                        </a>
                                        @include('cms.applicant.modal.hire-contract', ['data' => $d])

                                        <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                                            data-target="#hireRejectModal-{{ $d->id }}">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        @include('cms.applicant.modal.hire-reject', ['data' => $d])
                                    @endif

                                    @if ($d->status == 'accepted')
                                        <a href="{{ route('contract.download', $d->id) }}"
                                            class="btn btn-sm btn-success mr-1"><i class="fas fa-file-download"></i></a>
                                    @endif

                                    <a class="btn btn-sm btn-info" href="{{ route('show-servant', $d->servant->id) }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/summernote/summernote-bs4.min.css') }}">
@endpush

@push('custom-script')
    <script src="{{ asset('assets/vendor/summernote/summernote-bs4.min.js') }}"></script>
@endpush
