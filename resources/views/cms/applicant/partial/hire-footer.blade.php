<div class="d-flex flex-column">
    <div class="mb-2">
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
    </div>

    <div class="d-flex flex-wrap" style="gap: 4px;">
        @if ($d->status == 'pending')
            <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                data-target="#scheduleModal-{{ $d->id }}">
                <i class="fas fa-check"></i>
            </a>
            @include('cms.applicant.modal.schedule', [
                'data' => $d,
            ])
        @endif

        @hasrole('superadmin|admin')
            @if ($d->status == 'interview')
                <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                    data-target="#rejectModal-{{ $d->id }}">
                    <i class="fas fa-times"></i>
                </a>
                @include('cms.applicant.modal.reject', [
                    'data' => $d,
                ])
            @endif

            @if ($d->status === 'schedule')
                <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                    data-target="#interviewModal-{{ $d->id }}">
                    <i class="fas fa-calendar-day"></i>
                </a>
                @include('cms.applicant.modal.interview', [
                    'data' => $d,
                ])
            @endif

            @hasrole('superadmin')
                @if ($d->status === 'passed')
                    <td class="text-center">
                        <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                            data-target="#chooseModal-{{ $d->id }}"><i class="fas fa-check"></i></a>
                        @include('cms.applicant.modal.choose', [
                            'data' => $d,
                        ])

                        <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                            data-target="#rejectModal-{{ $d->id }}">
                            <i class="fas fa-times"></i>
                        </a>
                        @include('cms.applicant.modal.reject', [
                            'data' => $d,
                        ])
                    </td>
                @endif
            @endhasrole

            @if ($d->status === 'choose')
                <td class="text-center">
                    <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                        data-target="#verifyModal-{{ $d->id }}"><i class="fas fa-check-double"></i></a>
                    @include('cms.applicant.modal.verify', [
                        'data' => $d,
                    ])

                    <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                        data-target="#rejectModal-{{ $d->id }}">
                        <i class="fas fa-times"></i>
                    </a>
                    @include('cms.applicant.modal.reject', [
                        'data' => $d,
                    ])
                </td>
            @endif

            @if ($d->status === 'verify')
                <td class="text-center">
                    <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                        data-target="#agreeModal-{{ $d->id }}"><i class="fas fa-check-double"></i></a>
                    @include('cms.applicant.modal.agree', [
                        'data' => $d,
                    ])

                    <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                        data-target="#rejectModal-{{ $d->id }}">
                        <i class="fas fa-times"></i>
                    </a>
                    @include('cms.applicant.modal.reject', [
                        'data' => $d,
                    ])
                </td>
            @endif
        @endhasrole

        {{-- Button Gaji - Muncul ketika status accepted, schedule, atau interview --}}
        @hasrole('superadmin|admin')
            @if (in_array($d->status, ['accepted', 'schedule', 'interview']))
                <a href="#" class="btn btn-sm btn-warning mr-1" data-toggle="modal"
                    data-target="#salaryModal-{{ $d->id }}" title="Lihat Informasi Gaji">
                    <i class="fas fa-money-bill-wave"></i>
                </a>
                @include('cms.applicant.modal.salary', ['data' => $d])
            @endif
        @endhasrole

        {{-- Button Penjadwalan - Muncul ketika status schedule atau interview --}}
        @if (in_array($d->status, ['schedule', 'interview']))
            <a href="#" class="btn btn-sm btn-primary mr-1" data-toggle="modal"
                data-target="#scheduleInfoModal-{{ $d->id }}" title="Lihat Jadwal Interview">
                <i class="fas fa-calendar-alt"></i>
            </a>
            @include('cms.applicant.modal.schedule-info', ['data' => $d])
        @endif



        @if ($d->status == 'accepted')
            @php
                $alreadyComplained = $d->pengaduan->where('reporter_id', auth()->user()->id)->isNotEmpty();
            @endphp

            @if (!$alreadyComplained)
                <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                    data-target="#complaintModal-{{ $d->id }}">
                    <i class="fas fa-bullhorn"></i>
                </a>
                @include('cms.applicant.modal.complaint', ['data' => $d])
            @endif

        @endif

        @if ($d->status == 'pending')
            <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal" 
                data-target="#rejectModal-{{ $d->id }}">
                <i class="fas fa-times"></i>
            </a>
            @include('cms.applicant.modal.reject', [
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
