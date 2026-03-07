@extends('cms.layouts.main', ['title' => 'Dashboard Majikan'])

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Majikan</h1>
        {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> --}}
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pelamar (Pending)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pelamar (Proses)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['process'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pelamar (Diterima)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['accepted'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Pelamar (Ditolak)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['rejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Lowongan (Aktif)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['vacancy'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pekerja</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['worker'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-badge fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Pengaduan (Ditolak)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['rejectedComp'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pengaduan (Diterima)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['acceptedComp'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Jadwal Interview</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Pelamar</th>
                            <th>Status</th>
                            <th>Info Gaji & Kontrak</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datasApp as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->servant->name }}</td>
                                <td class="text-center">
                                    @if($data->status == 'interview')
                                        <span class="badge badge-info">Interview</span>
                                    @elseif($data->status == 'passed')
                                        <span class="badge badge-success">Lolos / Penawaran</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $data->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($data->status == 'passed' || ($data->salary && $data->salary > 0))
                                        <small class="d-block"><strong>Jenis:</strong> {{ $data->salary_type == 'contract' ? 'Kontrak' : 'Fee/Infal' }}</small>
                                        <small class="d-block"><strong>Gaji:</strong> Rp {{ number_format($data->salary, 0, ',', '.') }}</small>
                                        @if($data->salary_type == 'contract')
                                            <small class="d-block"><strong>Admin:</strong> Rp {{ number_format($data->admin_fee, 0, ',', '.') }}</small>
                                            <small class="d-block"><strong>Garansi:</strong> {{ $data->warranty_duration }}</small>
                                        @else
                                            @if($data->is_infal)
                                                 <small class="d-block"><strong>Mode:</strong> Infal ({{ $data->infal_frequency }})</small>
                                                 @if($data->infal_frequency == 'hourly')
                                                     <small class="d-block">Rate: Rp {{ number_format($data->infal_hourly_rate, 0, ',', '.') }}/jam</small>
                                                 @endif
                                            @else
                                                 <small class="d-block"><strong>Mode:</strong> Reguler</small>
                                            @endif
                                        @endif
                                        @if($data->work_start_date)
                                            <small class="d-block text-muted">Mulai: {{ \Carbon\Carbon::parse($data->work_start_date)->format('d M Y') }}</small>
                                        @endif
                                    @else
                                        @if($data->interview_date)
                                            <small><i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($data->interview_date)->format('d M Y') }}</small>
                                        @elseif($data->link_interview)
                                            <small><a href="{{ $data->link_interview }}" target="_blank">Link Meeting</a></small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">{!! $data->notes_interview !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
