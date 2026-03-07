@extends('cms.layouts.main', ['title' => 'Pengaduan'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Kelola Pengaduan</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengaduan</h6>
            @if(!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin'))
                @if(isset($activeContracts) && count($activeContracts) > 0)
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createComplaintModal">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Buat Pengaduan
                    </button>
                @else
                    <button type="button" class="btn btn-secondary btn-sm" disabled title="Anda belum memiliki kontrak aktif">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Buat Pengaduan
                    </button>
                    <small class="text-muted ml-2">Anda harus memiliki kontrak aktif untuk membuat pengaduan</small>
                @endif
            @endif
        </div>
        <div class="card-body">
        <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Pengadu</th>
                            <th>Nama Yang Diadukan</th>
                            <th>Jenis Pengaduan</th>
                            <th>Urgensi</th>
                            <th>Pesan Pengaduan</th>
                            <th>Status</th>
                            @hasrole('superadmin|admin')
                                <th>Aksi</th>
                            @else
                                <th>Catatan Penyelesaian</th>
                            @endhasrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->reporter->name ?? 'N/A' }}</td>
                                <td>{{ $data->reportedUser->name ?? 'N/A' }}</td>
                                <td>{{ $data->complaintType->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @php
                                        $urgencyLevel = $data->urgency_level ?? ($data->complaintType->default_urgency ?? 'LOW');
                                    @endphp
                                    <span class="badge badge-{{ match($urgencyLevel) {
                                        'LOW' => 'success',
                                        'MEDIUM' => 'info',
                                        'HIGH' => 'warning',
                                        'CRITICAL' => 'danger',
                                        default => 'secondary'
                                    } }}">
                                        {{ match($urgencyLevel) {
                                            'LOW' => 'Rendah',
                                            'MEDIUM' => 'Sedang',
                                            'HIGH' => 'Tinggi',
                                            'CRITICAL' => 'Kritis',
                                            default => $urgencyLevel
                                        } }}
                                    </span>
                                </td>
                                <td>{!! Str::limit($data->description, 50) !!}</td>
                                <td class="text-center">
                                    <span
                                        class="p-2 badge badge-{{ match ($data->status) {
                                            'resolved' => 'success',
                                            'open' => 'danger',
                                            'investigating' => 'warning',
                                            default => 'secondary',
                                        } }}">
                                        {{ match ($data->status) {
                                            'resolved' => 'Selesai',
                                            'open' => 'Baru',
                                            'investigating' => 'Dalam Proses',
                                            default => $data->status,
                                        } }}
                                    </span>
                                </td>
                                @hasrole('superadmin|admin')
                                    <td class="text-center" style="white-space: nowrap;">
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal{{ $data->id }}" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($data->status === 'open')
                                            <form action="{{ route('complaints.change', $data->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="investigating">
                                                <button type="submit" class="btn btn-sm btn-warning" title="Investigasi">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($data->status !== 'resolved')
                                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#resolveModal{{ $data->id }}" title="Selesaikan">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        @endif
                                    </td>
                                @else
                                    <td>
                                        @if($data->status === 'resolved' && $data->resolution_notes)
                                            <span class="text-success"><i class="fas fa-check-circle mr-1"></i></span>
                                            {!! Str::limit($data->resolution_notes, 80) !!}
                                            @if($data->resolvedBy)
                                                <br><small class="text-muted">Oleh: {{ $data->resolvedBy->name }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endhasrole
                            </tr>

                            {{-- Admin Detail Modal --}}
                            @hasrole('superadmin|admin')
                                @include('cms.complaint.modal.detail', ['data' => $data])
                                @include('cms.complaint.modal.resolve', ['data' => $data])
                            @endhasrole
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin'))
    <!-- Create Complaint Modal -->
    <div class="modal fade" id="createComplaintModal" tabindex="-1" role="dialog" aria-labelledby="createComplaintModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('complaints.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createComplaintModalLabel">Buat Pengaduan Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- Pilih Pekerja/Majikan yang diadukan --}}
                        <div class="form-group">
                            <label for="contract_select"><strong>
                                @if(auth()->user()->hasRole('majikan'))
                                    Pilih Pekerja yang Diadukan
                                @else
                                    Pilih Majikan yang Diadukan
                                @endif
                            </strong></label>
                            <select class="form-control" name="contract_id" id="contract_select" required>
                                <option value="">-- Pilih --</option>
                                @if(isset($activeContracts))
                                    @foreach($activeContracts as $contract)
                                        @php
                                            // Determine the other party
                                            $isMajikan = auth()->user()->hasRole('majikan');
                                            if ($isMajikan) {
                                                $otherParty = $contract->servant;
                                                $reportedUserId = $contract->servant_id;
                                            } else {
                                                // Pembantu: show the employer
                                                $otherParty = $contract->employe ?? ($contract->vacancy?->user);
                                                $reportedUserId = $contract->employe_id ?? $contract->vacancy?->user_id;
                                            }
                                            $otherName = $otherParty?->name ?? 'N/A';
                                            
                                            // Build label with type info
                                            $typeLabel = $contract->salary_type == 'contract' ? 'Kontrak' : 'Fee';
                                            if ($contract->is_infal && $contract->infal_frequency) {
                                                $typeLabel .= ' ' . match($contract->infal_frequency) {
                                                    'hourly' => 'Per Jam', 'daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', default => ''
                                                };
                                            }
                                        @endphp
                                        <option value="{{ $contract->id }}" data-reported-user-id="{{ $reportedUserId }}">
                                            {{ $otherName }} — {{ $typeLabel }} (Rp {{ number_format($contract->salary, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="form-text text-muted">
                                @if(auth()->user()->hasRole('majikan'))
                                    Pilih pekerja yang ingin Anda adukan.
                                @else
                                    Pilih majikan yang ingin Anda adukan.
                                @endif
                            </small>
                            {{-- Hidden field for reported_user_id, set via JS --}}
                            <input type="hidden" name="reported_user_id" id="reported_user_id" value="">
                        </div>

                        <div class="form-group">
                            <label for="complaint_type_id"><strong>Jenis Pengaduan</strong></label>
                            <select class="form-control" name="complaint_type_id" id="complaint_type_id" required>
                                <option value="">-- Pilih Jenis Pengaduan --</option>
                                @if(isset($urgencies))
                                    @foreach($urgencies as $urgency)
                                        <option value="{{ $urgency->id }}">
                                            {{ $urgency->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="form-text text-muted">Pilih jenis permasalahan yang ingin diadukan.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="message"><strong>Pesan Pengaduan</strong></label>
                            <textarea class="form-control" id="message" name="message" rows="4" required placeholder="Jelaskan masalah anda..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Kirim Pengaduan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('custom-script')
<script>
    // Auto-set reported_user_id when contract is selected
    $(document).ready(function() {
        $('#contract_select').change(function() {
            var selectedOption = $(this).find('option:selected');
            var reportedUserId = selectedOption.data('reported-user-id') || '';
            $('#reported_user_id').val(reportedUserId);
        });
    });
</script>
@endpush
