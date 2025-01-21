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
            @include('cms.applicant.modal.status.schedule', [
                'data' => $d,
            ])
        @endif

        @if ($d->status == 'interview')
            <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                data-target="#passedModal-{{ $d->id }}">
                <i class="fas fa-check"></i>
            </a>
            @include('cms.applicant.modal.status.passed', [
                'data' => $d,
            ])
        @endif

        @hasrole('superadmin|admin')
            @if ($d->status === 'schedule')
                <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                    data-target="#interviewModal-{{ $d->id }}">
                    <i class="fas fa-calendar-day"></i>
                </a>
                @include('cms.applicant.modal.status.interview', [
                    'data' => $d,
                ])
            @endif

            @hasrole('superadmin')
                @if ($d->status === 'passed')
                    <td class="text-center">
                        <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                            data-target="#chooseModal-{{ $d->id }}"><i class="fas fa-check"></i></a>
                        @include('cms.applicant.modal.status.choose', [
                            'data' => $d,
                        ])

                        <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                            data-target="#rejectModal-{{ $d->id }}">
                            <i class="fas fa-times"></i>
                        </a>
                        @include('cms.applicant.modal.status.reject', [
                            'data' => $d,
                        ])
                    </td>
                @endif
            @endhasrole

            @if ($d->status === 'choose')
                <td class="text-center">
                    <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                        data-target="#verifyModal-{{ $d->id }}"><i class="fas fa-check-double"></i></a>
                    @include('cms.applicant.modal.status.verify', [
                        'data' => $d,
                    ])

                    <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                        data-target="#rejectModal-{{ $d->id }}">
                        <i class="fas fa-times"></i>
                    </a>
                    @include('cms.applicant.modal.status.reject', [
                        'data' => $d,
                    ])
                </td>
            @endif

            @if ($d->status === 'verify')
                <td class="text-center">
                    <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                        data-target="#agreeModal-{{ $d->id }}"><i class="fas fa-check-double"></i></a>
                    @include('cms.applicant.modal.status.agree', [
                        'data' => $d,
                    ])

                    <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                        data-target="#rejectModal-{{ $d->id }}">
                        <i class="fas fa-times"></i>
                    </a>
                    @include('cms.applicant.modal.status.reject', [
                        'data' => $d,
                    ])
                </td>
            @endif
        @endhasrole

        @hasrole('majikan')
            @if ($d->status == 'verify')
                <a href="#" class="btn btn-sm btn-primary mr-1" data-toggle="modal"
                    data-target="#draftModal-{{ $d->id }}"><i class="fas fa-file-alt"></i></a>
                @include('cms.applicant.modal.status.draft', [
                    'data' => $d,
                ])
            @endif
        @endhasrole

        @if ($d->status == 'contract')
            <a href="#" class="btn btn-sm btn-primary mr-1" data-toggle="modal"
                data-target="#contractModal-{{ $d->id }}">
                <i class="fas fa-file-contract"></i>
            </a>
            @include('cms.applicant.modal.status.contract', [
                'data' => $d,
            ])
        @endif

        @if ($d->status == 'accepted')
            @php
                $hasComplaintWithSameServant = $d->complaint->contains(function ($complaint) use ($d) {
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

            <a href="{{ route('contract.download', ['applicationId' => $d->id]) }}" class="btn btn-sm btn-success mr-1"><i
                    class="fas fa-file-download"></i></a>
        @endif

        @if ($d->status == 'pending' || $d->status == 'interview')
            <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                data-target="#rejectModal-{{ $d->id }}">
                <i class="fas fa-times"></i>
            </a>
            @include('cms.applicant.modal.status.reject', [
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
