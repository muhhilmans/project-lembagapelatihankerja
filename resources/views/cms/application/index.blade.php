@extends('cms.layouts.main', ['title' => 'Lamaran Kerja'])

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Lamaran</h1>
        
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
                <a class="dropdown-item {{ $type == 'all' ? 'active' : '' }}" href="{{ route('application.index', ['type' => 'all']) }}">
                    <i class="fas fa-list mr-2"></i>Semua
                </a>
                <a class="dropdown-item {{ $type == 'hire' ? 'active' : '' }}" href="{{ route('application.index', ['type' => 'hire']) }}">
                    <i class="fas fa-handshake mr-2"></i>Hire
                </a>
                <a class="dropdown-item {{ $type == 'mandiri' ? 'active' : '' }}" href="{{ route('application.index', ['type' => 'mandiri']) }}">
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
                        <i class="fas fa-handshake mr-2"></i>Lamaran - Hire
                    </h6>
                    <span class="badge badge-primary">{{ $hireData->count() }} Lamaran</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama Majikan</th>
                                    @hasrole('superadmin|admin')
                                        <th>Nama Pelamar</th>
                                    @endhasrole
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Gaji Pokok</th>
                                    @hasrole('superadmin|pembantu')
                                        @if ($hireData->contains(fn($data) => in_array($data->status, ['passed', 'accepted'])))
                                            <th>Aksi</th>
                                        @endif
                                    @endhasrole
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hireData as $data)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $data->employe->name }}</td>
                                        @hasrole('superadmin|admin')
                                            <td>{{ $data->servant->name }}</td>
                                        @endhasrole
                                        <td class="text-center">
                                            @if ($data->status === 'schedule' || $data->status === 'interview')
                                                {{ \Carbon\Carbon::parse($data->interview_date ? $data->interview_date : '')->format('d-M-Y') }}
                                            @elseif ($data->status === 'accepted')
                                                {{ \Carbon\Carbon::parse($data->work_start_date ? $data->work_start_date : '')->format('d-M-Y') }}
                                            @elseif ($data->status === 'rejected' || $data->status === 'laidoff')
                                                {{ \Carbon\Carbon::parse($data->updated_at ? $data->updated_at : '')->format('d-M-Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @if ($data->status === 'interview' || $data->status === 'schedule')
                                            <td class="text-center">
                                                {!! $data->notes_interview !!}
                                                @if ($data->status === 'interview')
                                                    @if ($data->link_interview != null)
                                                        Link Interview : <a href="{{ $data->link_interview }}" target="_blank"
                                                            rel="noopener noreferrer">{{ $data->link_interview }}</a>
                                                    @endif
                                                @endif
                                            </td>
                                        @elseif ($data->status === 'verify')
                                            <td class="text-center">{!! $data->notes_verify !!}</td>
                                        @elseif ($data->status === 'rejected' || $data->status === 'laidoff')
                                            <td class="text-center">{!! $data->notes_rejected !!}</td>
                                        @else
                                            <td class="text-center">-</td>
                                        @endif
                                        <td class="text-center">
                                            <span
                                                class="p-2 badge badge-{{ match ($data->status) {
                                                    'accepted' => 'success',
                                                    'rejected' => 'danger',
                                                    'laidoff' => 'danger',
                                                    'pending' => 'warning',
                                                    'interview' => 'info',
                                                    'schedule' => 'info',
                                                    'verify' => 'success',
                                                    'contract' => 'success',
                                                    default => 'secondary',
                                                } }}">
                                                {{ match ($data->status) {
                                                    'accepted' => 'Diterima',
                                                    'rejected' => 'Ditolak',
                                                    'laidoff' => 'Diberhentikan',
                                                    'pending' => 'Pending',
                                                    'schedule' => 'Penjadwalan',
                                                    'interview' => 'Interview',
                                                    'passed' => 'Lolos Interview',
                                                    'choose' => 'Verifikasi',
                                                    'verify' => 'Persiapan Kerja',
                                                    'contract' => 'Perjanjian',
                                                    default => 'Status Tidak Diketahui',
                                                } }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            Rp. {{ number_format($data->salary, 0, ',', '.') }}
                                        </td>

                                        @if ($data->status === 'passed' || $data->status === 'accepted')
                                            <td class="text-center">
                                                @if ($data->status === 'passed')
                                                    <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                                        data-target="#passedModal-{{ $data->id }}"><i
                                                            class="fas fa-building"></i></a>
                                                    @include('cms.application.modal.passed-hire', [
                                                        'data' => $data,
                                                    ])
                                                @endif

                                                @if ($data->status === 'accepted')
                                                    @php
                                                        $hasComplaintWithSameServant = $data->complaint->contains(function (
                                                            $complaint,
                                                        ) use ($data) {
                                                            return $complaint->servant_id == $data->servant_id;
                                                        });
                                                    @endphp

                                                    @if (!$hasComplaintWithSameServant)
                                                        <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                                                            data-target="#complaintModal-{{ $data->id }}">
                                                            <i class="fas fa-bullhorn"></i>
                                                        </a>
                                                        @include('cms.application.modal.complaint', [
                                                            'data' => $data,
                                                        ])
                                                    @endif
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @elseif($type == 'hire')
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Belum ada lamaran Hire</p>
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
                        <i class="fas fa-user mr-2"></i>Lamaran - Mandiri
                    </h6>
                    <span class="badge badge-success">{{ $indieData->count() }} Lamaran</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama Lowongan</th>
                                    <th>Nama Majikan</th>
                                    @hasrole('superadmin|admin')
                                        <th>Nama Pelamar</th>
                                    @endhasrole
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Gaji Pokok</th>
                                    @hasrole('superadmin|pembantu')
                                        @if ($indieData->contains(fn($data) => in_array($data->status, ['passed', 'accepted'])))
                                            <th>Aksi</th>
                                        @endif
                                    @endhasrole
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($indieData as $data)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $data->vacancy->title }}</td>
                                        <td>{{ $data->vacancy->user->name }}</td>
                                        @hasrole('superadmin|admin')
                                            <td>{{ $data->servant->name }}</td>
                                        @endhasrole
                                        <td class="text-center">
                                            @if ($data->status === 'schedule' || $data->status === 'interview')
                                                {{ \Carbon\Carbon::parse($data->interview_date ? $data->interview_date : '')->format('d-M-Y') }}
                                            @elseif ($data->status === 'accepted')
                                                {{ \Carbon\Carbon::parse($data->work_start_date ? $data->work_start_date : '')->format('d-M-Y') }}
                                            @elseif ($data->status === 'rejected' || $data->status === 'laidoff')
                                                {{ \Carbon\Carbon::parse($data->updated_at ? $data->updated_at : '')->format('d-M-Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @if ($data->status === 'interview' || $data->status === 'schedule')
                                            <td class="text-center">
                                                {!! $data->notes_interview !!}
                                                @if ($data->status === 'interview')
                                                    @if ($data->link_interview != null)
                                                        Link Interview : <a href="{{ $data->link_interview }}" target="_blank"
                                                            rel="noopener noreferrer">{{ $data->link_interview }}</a>
                                                    @endif
                                                @endif
                                            </td>
                                        @elseif ($data->status === 'verify')
                                            <td class="text-center">{!! $data->notes_verify !!}</td>
                                        @elseif ($data->status === 'rejected' || $data->status === 'laidoff')
                                            <td class="text-center">{!! $data->notes_rejected !!}</td>
                                        @else
                                            <td class="text-center">-</td>
                                        @endif
                                        <td class="text-center">
                                            <span
                                                class="p-2 badge badge-{{ match ($data->status) {
                                                    'accepted' => 'success',
                                                    'rejected' => 'danger',
                                                    'laidoff' => 'danger',
                                                    'pending' => 'warning',
                                                    'interview' => 'info',
                                                    'schedule' => 'info',
                                                    'verify' => 'success',
                                                    'contract' => 'success',
                                                    default => 'secondary',
                                                } }}">
                                                {{ match ($data->status) {
                                                    'accepted' => 'Diterima',
                                                    'rejected' => 'Ditolak',
                                                    'laidoff' => 'Diberhentikan',
                                                    'pending' => 'Pending',
                                                    'schedule' => 'Penjadwalan',
                                                    'interview' => 'Interview',
                                                    'passed' => 'Lolos Interview',
                                                    'choose' => 'Verifikasi',
                                                    'verify' => 'Persiapan Kerja',
                                                    'contract' => 'Perjanjian',
                                                    default => 'Status Tidak Diketahui',
                                                } }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            Rp. {{ number_format($data->salary, 0, ',', '.') }}
                                        </td>

                                        @hasrole('superadmin|pembantu')
                                            @if ($data->status === 'accepted' || $data->status === 'passed')
                                                <td class="text-center">
                                                    @if ($data->status === 'passed')
                                                        <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                                            data-target="#passedModal-{{ $data->id }}"><i
                                                                class="fas fa-building"></i></a>
                                                        @include('cms.application.modal.passed', [
                                                            'data' => $data,
                                                        ])
                                                    @endif

                                                    @if ($data->status === 'accepted')
                                                        @php
                                                            $hasComplaintWithSameServant = $data->complaint->contains(function (
                                                                $complaint,
                                                            ) use ($data) {
                                                                return $complaint->servant_id == $data->servant_id;
                                                            });
                                                        @endphp

                                                        @if (!$hasComplaintWithSameServant)
                                                            <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                                                                data-target="#complaintModal-{{ $data->id }}">
                                                                <i class="fas fa-bullhorn"></i>
                                                            </a>
                                                            @include('cms.application.modal.complaint', [
                                                                'data' => $data,
                                                            ])
                                                        @endif
                                                    @endif
                                                </td>
                                            @endif
                                        @endhasrole
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @elseif($type == 'mandiri')
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Belum ada lamaran Mandiri</p>
                </div>
            </div>
        @endif
    @endif

    @if($hireData->isEmpty() && $indieData->isEmpty() && $type == 'all')
        <div class="card shadow mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted">Belum ada lamaran</p>
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
