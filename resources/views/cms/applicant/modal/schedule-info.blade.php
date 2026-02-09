{{-- Modal Penjadwalan - Schedule/Interview Information --}}
<div class="modal fade" id="scheduleInfoModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="scheduleInfoModalLabel-{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="scheduleInfoModalLabel-{{ $data->id }}">
                    <i class="fas fa-calendar-alt mr-2"></i>Informasi Penjadwalan Interview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-info mx-auto d-flex align-items-center justify-content-center" 
                        style="width: 80px; height: 80px;">
                        <i class="fas fa-calendar-check fa-2x text-white"></i>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-user mr-2"></i>{{ $data->servant->name ?? 'Pembantu' }}
                        </h6>

                        <ul class="list-group list-group-flush">
                            {{-- Status --}}
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">
                                    <i class="fas fa-info-circle mr-2"></i>Status
                                </span>
                                <span class="badge badge-{{ $data->status == 'schedule' ? 'warning' : 'info' }} p-2">
                                    {{ $data->status == 'schedule' ? 'Menunggu Interview' : 'Interview Dijadwalkan' }}
                                </span>
                            </li>

                            {{-- Tanggal Interview --}}
                            @if ($data->interview_date)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">
                                        <i class="fas fa-calendar-day mr-2"></i>Tanggal Interview
                                    </span>
                                    <span class="font-weight-bold text-primary">
                                        {{ \Carbon\Carbon::parse($data->interview_date)->format('d F Y') }}
                                    </span>
                                </li>

                                {{-- Waktu Interview (jika ada) --}}
                                @if ($data->interview_time)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span class="text-muted">
                                            <i class="fas fa-clock mr-2"></i>Waktu Interview
                                        </span>
                                        <span class="font-weight-bold">
                                            {{ \Carbon\Carbon::parse($data->interview_time)->format('H:i') }} WIB
                                        </span>
                                    </li>
                                @endif

                                {{-- Countdown --}}
                                @php
                                    $interviewDate = \Carbon\Carbon::parse($data->interview_date)->startOfDay();
                                    $now = \Carbon\Carbon::now()->startOfDay();
                                    $diffInDays = (int) $now->diffInDays($interviewDate, false);
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">
                                        <i class="fas fa-hourglass-half mr-2"></i>Countdown
                                    </span>
                                    <span class="font-weight-bold {{ $diffInDays < 0 ? 'text-danger' : ($diffInDays <= 3 ? 'text-warning' : 'text-success') }}">
                                        @if ($diffInDays < 0)
                                            {{ abs($diffInDays) }} hari yang lalu
                                        @elseif ($diffInDays == 0)
                                            Hari ini!
                                        @else
                                            {{ $diffInDays }} hari lagi
                                        @endif
                                    </span>
                                </li>
                            @endif

                            {{-- Link Interview (jika ada) --}}
                            @if ($data->link_interview)
                                <li class="list-group-item px-0">
                                    <span class="text-muted d-block mb-2">
                                        <i class="fas fa-link mr-2"></i>Link Interview
                                    </span>
                                    <a href="{{ $data->link_interview }}" target="_blank" rel="noopener noreferrer" 
                                        class="btn btn-outline-primary btn-sm btn-block">
                                        <i class="fas fa-external-link-alt mr-2"></i>Buka Link Interview
                                    </a>
                                </li>
                            @endif

                            {{-- Catatan Interview (jika ada) --}}
                            @if ($data->notes_interview)
                                <li class="list-group-item px-0">
                                    <span class="text-muted d-block mb-2">
                                        <i class="fas fa-sticky-note mr-2"></i>Catatan
                                    </span>
                                    <div class="p-3 bg-light rounded">
                                        {!! $data->notes_interview !!}
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
