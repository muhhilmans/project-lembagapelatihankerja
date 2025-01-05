<div class="row">
    @if ($applications->isEmpty())
        <div class="col-12 text-center">
            <p class="text-muted text-center">Belum ada pelamar</p>
        </div>
    @else
        @foreach ($applications as $d)
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
                    <div class="card-footer text-right">
                        <div class="d-flex justify-content-between align-items-center">
                            <span
                                class="p-2 badge badge-{{ match ($d->status) {
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
                                {{ match ($d->status) {
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

                            <div class="row">
                                @if ($d->status == 'pending')
                                    <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                                        data-target="#scheduleModal-{{ $d->id }}">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    @include('cms.vacancy.modal.status.schedule', [
                                        'data' => $d,
                                    ])
                                @endif

                                @if ($d->status == 'interview')
                                    <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                                        data-target="#passedModal-{{ $d->id }}">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    @include('cms.vacancy.modal.status.passed', [
                                        'data' => $d,
                                    ])
                                @endif

                                @hasrole('superadmin|admin')
                                    @if ($d->status === 'schedule')
                                        <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                                            data-target="#interviewModal-{{ $d->id }}">
                                            <i class="fas fa-calendar-day"></i>
                                        </a>
                                        @include('cms.vacancy.modal.status.interview', [
                                            'data' => $d,
                                        ])
                                    @endif

                                    @if ($d->status === 'passed')
                                        <td class="text-center">
                                            <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                                                data-target="#chooseModal-{{ $d->id }}"><i
                                                    class="fas fa-check"></i></a>
                                            @include('cms.vacancy.modal.status.choose', [
                                                'data' => $d,
                                            ])

                                            <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                                                data-target="#rejectModal-{{ $d->id }}">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            @include('cms.vacancy.modal.status.reject', [
                                                'data' => $d,
                                            ])
                                        </td>
                                    @endif

                                    @if ($d->status === 'choose')
                                        <td class="text-center">
                                            <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                                                data-target="#verifyModal-{{ $d->id }}"><i
                                                    class="fas fa-check-double"></i></a>
                                            @include('cms.vacancy.modal.status.verify', [
                                                'data' => $d,
                                            ])

                                            <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                                                data-target="#rejectModal-{{ $d->id }}">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            @include('cms.vacancy.modal.status.reject', [
                                                'data' => $d,
                                            ])
                                        </td>
                                    @endif

                                    @if ($d->status === 'verify')
                                        <td class="text-center">
                                            <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                                                data-target="#agreeModal-{{ $d->id }}"><i
                                                    class="fas fa-check-double"></i></a>
                                            @include('cms.vacancy.modal.status.agree', [
                                                'data' => $d,
                                            ])

                                            <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                                                data-target="#rejectModal-{{ $d->id }}">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            @include('cms.vacancy.modal.status.reject', [
                                                'data' => $d,
                                            ])
                                        </td>
                                    @endif
                                @endhasrole

                                @hasrole('majikan')
                                    @if ($d->status == 'verify')
                                        <a href="#" class="btn btn-sm btn-primary mr-1" data-toggle="modal"
                                            data-target="#draftModal-{{ $d->id }}"><i
                                                class="fas fa-file-alt"></i></a>
                                        @include('cms.vacancy.modal.status.draft', [
                                            'data' => $d,
                                        ])
                                    @endif
                                @endhasrole

                                @if ($d->status == 'contract')
                                    <a href="#" class="btn btn-sm btn-primary mr-1" data-toggle="modal"
                                        data-target="#contractModal-{{ $d->id }}">
                                        <i class="fas fa-file-contract"></i>
                                    </a>
                                    @include('cms.vacancy.modal.status.contract', [
                                        'data' => $d,
                                    ])
                                @endif

                                @if ($d->status == 'accepted')
                                    @php
                                        $hasComplaintWithSameServant = $d->complaint->contains(function (
                                            $complaint,
                                        ) use ($d) {
                                            return $complaint->servant_id == $d->servant_id;
                                        });
                                    @endphp

                                    @if (!$hasComplaintWithSameServant)
                                        <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                                            data-target="#complaintModal-{{ $d->id }}">
                                            <i class="fas fa-bullhorn"></i>
                                        </a>
                                        @include('cms.applicant.modal.complaint', ['data' => $d])
                                    @endif

                                    <a href="{{ route('contract.download', $d->id) }}"
                                        class="btn btn-sm btn-success mr-1"><i class="fas fa-file-download"></i></a>
                                @endif

                                @if ($d->status == 'pending' || $d->status == 'interview')
                                    <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                                        data-target="#rejectModal-{{ $d->id }}">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    @include('cms.vacancy.modal.status.reject', [
                                        'data' => $d,
                                    ])
                                @endif

                                <a class="btn btn-sm btn-info" href="#" data-toggle="modal"
                                    data-target="#servantDetailsModal-{{ $d->id }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @include('cms.vacancy.modal.servant-detail', ['data' => $d])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
