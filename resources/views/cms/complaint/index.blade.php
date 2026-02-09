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
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal{{ $data->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                @endhasrole
                            </tr>
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
                            <label for="message">Pesan Pengaduan</label>
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
    $('#application_id').change(function() {
        // We need to pass the ID of the person we are reporting.
        // Since we don't have that ID easily in the option value (only app ID),
        // we might need to change the option value or use data attributes.
        // Let's reload the page with data attributes? No.
        // Better: render data attributes on the options.
    });
</script>
@endpush

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/summernote/summernote-bs4.min.css') }}">
@endpush

@push('custom-script')
    <script src="{{ asset('assets/vendor/summernote/summernote-bs4.min.js') }}"></script>
@endpush
