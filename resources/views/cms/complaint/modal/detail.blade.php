{{-- Detail Pengaduan Modal for Admin/Superadmin --}}
<div class="modal fade" id="detailModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel{{ $data->id }}"><i class="fas fa-exclamation-triangle mr-2"></i> Detail Pengaduan</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Info Cards Row --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card border-left-primary shadow-sm h-100">
                            <div class="card-body py-2">
                                <small class="text-muted d-block"><i class="fas fa-user mr-1"></i> Pengadu</small>
                                <strong>{{ $data->reporter->name ?? 'N/A' }}</strong>
                                @php
                                    $reporterRole = $data->reporter?->roles?->first()?->name;
                                @endphp
                                <span class="badge badge-{{ $reporterRole == 'majikan' ? 'info' : 'success' }} ml-1" style="font-size: 0.7rem;">
                                    {{ $reporterRole == 'majikan' ? 'Majikan' : 'Pembantu' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-danger shadow-sm h-100">
                            <div class="card-body py-2">
                                <small class="text-muted d-block"><i class="fas fa-user-slash mr-1"></i> Terlapor</small>
                                <strong>{{ $data->reportedUser->name ?? 'N/A' }}</strong>
                                @php
                                    $reportedRole = $data->reportedUser?->roles?->first()?->name;
                                @endphp
                                <span class="badge badge-{{ $reportedRole == 'majikan' ? 'info' : 'success' }} ml-1" style="font-size: 0.7rem;">
                                    {{ $reportedRole == 'majikan' ? 'Majikan' : 'Pembantu' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Jenis & Urgensi --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1"><i class="fas fa-tag mr-1"></i> Jenis Pengaduan</small>
                        <span class="font-weight-bold">{{ $data->complaintType->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block mb-1"><i class="fas fa-exclamation-circle mr-1"></i> Urgensi</small>
                        @php
                            $urgencyLevel = $data->urgency_level ?? ($data->complaintType->default_urgency ?? 'LOW');
                        @endphp
                        <span class="badge badge-{{ match($urgencyLevel) {
                            'LOW' => 'success', 'MEDIUM' => 'info', 'HIGH' => 'warning', 'CRITICAL' => 'danger', default => 'secondary'
                        } }} px-2 py-1">
                            {{ match($urgencyLevel) {
                                'LOW' => 'Rendah', 'MEDIUM' => 'Sedang', 'HIGH' => 'Tinggi', 'CRITICAL' => 'Kritis', default => $urgencyLevel
                            } }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block mb-1"><i class="fas fa-info-circle mr-1"></i> Status</small>
                        <span class="badge badge-{{ match($data->status) {
                            'resolved' => 'success', 'open' => 'danger', 'investigating' => 'warning', default => 'secondary'
                        } }} px-2 py-1">
                            {{ match($data->status) {
                                'resolved' => 'Selesai', 'open' => 'Baru', 'investigating' => 'Dalam Proses', default => $data->status
                            } }}
                        </span>
                    </div>
                </div>

                {{-- Kontrak Info --}}
                @if($data->application)
                <div class="row mb-3">
                    <div class="col-12">
                        <small class="text-muted d-block mb-1"><i class="fas fa-file-contract mr-1"></i> Kontrak Terkait</small>
                        <div class="border rounded p-2 bg-light">
                            <strong>{{ $data->application->vacancy?->title ?? 'Kontrak Langsung' }}</strong>
                            <span class="text-muted ml-2">
                                | Tipe: {{ $data->application->salary_type == 'contract' ? 'Kontrak' : 'Fee' }}
                                @if($data->application->is_infal && $data->application->infal_frequency)
                                    ({{ match($data->application->infal_frequency) { 'hourly' => 'Per Jam', 'daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', default => '' } }})
                                @endif
                                | Gaji: Rp {{ number_format($data->application->salary, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Deskripsi --}}
                <div class="mb-3">
                    <small class="text-muted d-block mb-1"><i class="fas fa-comment-alt mr-1"></i> Kronologi / Deskripsi</small>
                    <div class="border rounded p-3 bg-light" style="min-height: 80px;">
                        {!! $data->description !!}
                    </div>
                </div>

                {{-- Resolved At & Resolution Notes --}}
                @if($data->resolved_at)
                <div class="alert alert-success mb-2">
                    <i class="fas fa-check-circle mr-1"></i> Diselesaikan pada: <strong>{{ $data->resolved_at->format('d F Y H:i') }}</strong>
                    @if($data->resolvedBy)
                        <br><small>Oleh: <strong>{{ $data->resolvedBy->name }}</strong></small>
                    @endif
                </div>
                @endif

                @if($data->resolution_notes)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1"><i class="fas fa-clipboard-check mr-1"></i> Catatan Penyelesaian</small>
                    <div class="border rounded p-3 bg-light border-success" style="min-height: 60px; border-left: 4px solid #1cc88a !important;">
                        {!! nl2br(e($data->resolution_notes)) !!}
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                @if($data->status !== 'resolved')
                    @if($data->status === 'open')
                        <form action="{{ route('complaints.change', $data->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="investigating">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-search mr-1"></i> Investigasi
                            </button>
                        </form>
                    @endif
                    <button type="button" class="btn btn-success" data-dismiss="modal" onclick="setTimeout(function(){ $('#resolveModal{{ $data->id }}').modal('show'); }, 400);">
                        <i class="fas fa-check-circle mr-1"></i> Selesaikan
                    </button>
                @endif
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
