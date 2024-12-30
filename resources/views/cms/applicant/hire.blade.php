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
                                    <li class="mb-1"><strong>Tanggal Interview:</strong>
                                        {{ \Carbon\Carbon::parse($d->interview_date)->format('d-m-Y') }}</li>
                                    <li class="mb-1"><strong>Catatan:</strong> {!! $d->notes_interview !!}</li>
                                @endif
                                @if ($d->salary != null)
                                    @php
                                        $salary = $d->salary;
                                        $service = $salary * 0.025;
                                        $gaji = $salary - $service;
                                    @endphp

                                    <li><strong>Gaji:</strong> Rp. {{ number_format($gaji, 0, ',', '.') }}</li>
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
                            @include('cms.applicant.partial.hire-footer', ['d' => $d])
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
