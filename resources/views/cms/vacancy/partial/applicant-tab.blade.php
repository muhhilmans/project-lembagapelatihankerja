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
                        @if ($d->interview_date != null)
                            <p class="card-text"><strong>Tanggal Interview:</strong>
                                {{ \Carbon\Carbon::parse($d->interview_date)->format('d-m-Y') }}</p>
                        @endif

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
                                    'pending' => 'warning',
                                    'interview' => 'info',
                                    default => 'secondary',
                                } }}">
                                {{ match ($d->status) {
                                    'accepted' => 'Diterima',
                                    'rejected' => 'Ditolak',
                                    'pending' => 'Pending',
                                    'interview' => 'Interview',
                                    'passed' => 'Lolos Interview',
                                    'choose' => 'Pending Verifikasi',
                                    'verify' => 'Verifikasi',
                                    default => 'Status Tidak Diketahui',
                                } }}
                            </span>

                            <div class="row">
                                @if ($d->status == 'pending')
                                    <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                                        data-target="#interviewModal-{{ $d->id }}">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    @include('cms.vacancy.modal.status.interview', [
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

                                @if ($d->status == 'passed')
                                    <a href="#" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                                        data-target="#chooseModal-{{ $d->id }}">
                                        <i class="fas fa-check-double"></i>
                                    </a>
                                    @include('cms.vacancy.modal.status.choose', [
                                        'data' => $d,
                                    ])
                                @endif

                                @if ($d->status == 'verify')
                                    <a href="#" class="btn btn-sm btn-primary mr-1" data-toggle="modal"
                                        data-target="#contractModal-{{ $d->id }}">
                                        <i class="fas fa-file-contract"></i>
                                    </a>
                                    @include('cms.vacancy.modal.status.contract', [
                                        'data' => $d,
                                    ])
                                @endif

                                @if ($d->status == 'accepted')
                                    <a href="{{ route('contract.download', $d->id) }}"
                                        class="btn btn-sm btn-success mr-1"><i class="fas fa-file-download"></i></a>
                                @endif

                                @if ($d->status != 'rejected')
                                    <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                                        data-target="#rejectModal-{{ $d->id }}">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    @include('cms.vacancy.modal.status.reject', [
                                        'data' => $d,
                                    ])
                                @endif

                                <a class="btn btn-sm btn-info" href="#" data-toggle="modal" data-target="#servantDetailsModal-{{ $d->id }}">
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