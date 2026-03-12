@extends('cms.layouts.main', ['title' => 'Pekerja'])

@push('custom-style')
<style>
.filter-grid {
    display:grid;
    grid-template-columns: repeat(4, 1fr);
    gap:15px;
}
@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
}
.badge-kontrak { background:#2f80ed; color:white; padding:4px 10px; border-radius:12px; font-size:12px;}
.badge-fee { background:#27ae60; color:white; padding:4px 10px; border-radius:12px; font-size:12px;}
</style>
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="mb-4 d-flex justify-content-between align-items-baseline">
        @hasrole('superadmin|admin|owner|majikan')
            <h1 class="h3 text-gray-800">Daftar Pembantu Bekerja</h1>
        @endhasrole
        @hasrole('pembantu')
            <h1 class="h3 text-gray-800">Daftar Pekerjaan</h1>
        @endhasrole
        @hasrole('superadmin|admin|owner')
            <div>
                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#downloadModal"><i
                        class="fas fa-download"></i> PDF</a>
                <a href="#" class="btn btn-success" data-toggle="modal" data-target="#excelModal"><i class="fas fa-file-excel"></i> Excel</a>
                @include('cms.servant.modal.export')
                @include('cms.servant.modal.export-excel')
            </div>
        @endhasrole
    </div>

    @hasrole('superadmin|admin|owner')
        <!-- NEW SUPERADMIN UI -->
        <div class="card shadow mb-4" style="border-radius:8px;">
            <div class="card-header py-3 bg-white" style="border-bottom:none;">
                <h6 class="m-0 font-weight-bold" style="color:#333; font-size:16px;">Filter</h6>
            </div>
            <div class="card-body pt-0">
                <div class="filter-grid mb-2">
                    <select class="form-control mr-3 mb-2 w-auto custom-filter" id="filterJenis" style="border-radius:6px; font-size:14px; border-color:#e0e0e0; box-shadow:0 2px 4px rgba(0,0,0,0.02)">
                        <option value="">Semua Jenis</option>
                        <option value="Kontrak">Kontrak</option>
                        <option value="Fee">Fee</option>
                        <option value="Belum Diatur">Belum Diatur</option>
                    </select>

                    <select id="filterTipeFee" class="form-control" style="border-radius:6px; padding:8px;">
                        <option value="">Tipe Fee</option>
                        <option value="Bulanan">Bulanan</option>
                        <option value="Mingguan">Mingguan</option>
                        <option value="Harian">Harian</option>
                        <option value="Jam">Jam</option>
                    </select>

                    <select id="filterLamaKerja" class="form-control" style="border-radius:6px; padding:8px;">
                        <option value="">Lama Bekerja</option>
                        <option value="< 1 Bulan">< 1 Bulan</option>
                        <option value="1-3 Bulan">1-3 Bulan</option>
                        <option value="3-6 Bulan">3-6 Bulan</option>
                        <option value="> 6 Bulan">> 6 Bulan</option>
                    </select>

                    <select id="filterStatusBayar" class="form-control" style="border-radius:6px; padding:8px;">
                        <option value="">Status Pembayaran</option>
                        <option value="Lunas oleh Majikan">Lunas oleh Majikan</option>
                        <option value="Lunas oleh Sipembantu">Lunas oleh Sipembantu (Admin)</option>
                        <option value="Belum Bayar">Belum Bayar</option>
                    </select>

                    <select class="form-control mr-3 mb-2 w-auto custom-filter" id="filterStatusKontrak" style="border-radius:6px; font-size:14px; border-color:#e0e0e0; box-shadow:0 2px 4px rgba(0,0,0,0.02)">
                        <option value="">Status Kontrak</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Habis">Habis</option>
                        <option value="Belum Diatur">- / Belum Diatur</option>
                    </select>

                    <input type="text" id="filterNama" class="form-control" placeholder="Cari Nama ART / Majikan" style="border-radius:6px; padding:8px;">

                    <button id="btnApplyFilter" class="btn" style="background:#2f80ed; color:white; border-radius:6px; padding:8px 15px;">Apply Filter</button>
                    <button id="btnResetFilter" class="btn btn-secondary" style="border-radius:6px; padding:8px 15px;">Reset</button>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4" style="border-radius:8px;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="dataTableAdmin" width="100%" cellspacing="0">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee;">
                                <th style="border:none; padding:12px;">Nama ART</th>
                                <th style="border:none; padding:12px;">Majikan</th>
                                <th style="border:none; padding:12px;">Jenis</th>
                                <th style="border:none; padding:12px;">Lama Kerja</th>
                                <th style="border:none; padding:12px;">Gaji</th>
                                <th style="border:none; padding:12px;">Rekening / BPJS</th>
                                <th style="border:none; padding:12px;">Status Bayar</th>
                                <th style="border:none; padding:12px;">Status Kontrak</th>
                                <th style="border:none; padding:12px;">Aksi</th>
                                <th class="d-none">Kol Filter 1</th>
                                <th class="d-none">Kol Filter 2</th>
                                <th class="d-none">Kol Filter 3</th>
                                <th class="d-none">Kol Filter 4</th>
                                <th class="d-none">Kol Filter 5</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $data)
                                @php
                                    $namaArt = $data->servant->name;
                                    $namaMajikan = $data->vacancy_id != null && $data->vacancy && $data->vacancy->user ? $data->vacancy->user->name : ($data->employe ? $data->employe->name : '-');
                                    
                                    $jenis = '<span class="text-muted small">-</span>';
                                    $jenisRaw = 'Belum Diatur';
                                    $tipeFeeRaw = 'Belum Diatur';
                                    if ($data->salary_type == 'contract') {
                                        $jenis = '<span class="badge-kontrak">Kontrak</span>';
                                        $jenisRaw = 'Kontrak';
                                    } elseif ($data->salary_type == 'fee' && $data->is_infal) {
                                        $freqLabel = match($data->infal_frequency) {
                                            'hourly' => 'Per Jam',
                                            'daily' => 'Harian',
                                            'weekly' => 'Mingguan',
                                            'monthly' => 'Bulanan',
                                            default => 'Sementara'
                                        };
                                        $tipeFeeRaw = match($data->infal_frequency) {
                                            'hourly' => 'Jam',
                                            'daily' => 'Harian',
                                            'weekly' => 'Mingguan',
                                            'monthly' => 'Bulanan',
                                            default => 'Sementara'
                                        };
                                        $jenis = '<span class="badge-fee">Fee</span> <br><small class="text-muted">('.$freqLabel.')</small>';
                                        $jenisRaw = 'Fee';
                                    } elseif ($data->salary_type == 'fee') {
                                        $freqLabel = match($data->infal_frequency) {
                                            'hourly' => 'Per Jam',
                                            'daily' => 'Harian',
                                            'weekly' => 'Mingguan',
                                            'monthly' => 'Bulanan',
                                            default => 'Bulanan'
                                        };
                                        $tipeFeeRaw = match($data->infal_frequency) {
                                            'hourly' => 'Jam',
                                            'daily' => 'Harian',
                                            'weekly' => 'Mingguan',
                                            'monthly' => 'Bulanan',
                                            default => 'Bulanan'
                                        };
                                        $jenis = '<span class="badge-fee">Fee</span> <br><small class="text-muted">('.$freqLabel.')</small>';
                                        $jenisRaw = 'Fee';
                                    }

                                    $start = \Carbon\Carbon::parse($data->work_start_date);
                                    $diffBulan = (int) $start->diffInMonths(now());
                                    $diffHari = (int) $start->diffInDays(now());
                                    $lamaKerja = $diffBulan > 0 ? $diffBulan . ' Bulan' : $diffHari . ' Hari';
                                    
                                    if ($diffBulan < 1) $lamaKerjaRaw = '< 1 Bulan';
                                    elseif ($diffBulan <= 3) $lamaKerjaRaw = '1-3 Bulan';
                                    elseif ($diffBulan <= 6) $lamaKerjaRaw = '3-6 Bulan';
                                    else $lamaKerjaRaw = '> 6 Bulan';

                                    $latestSalary = \App\Models\WorkerSalary::where('application_id', $data->id)->orderBy('created_at', 'desc')->first();
                                    $statusBayar = '<span class="badge" style="background:#f39c12; color:white; padding:4px 10px; border-radius:12px; font-size:12px;">Belum Bayar</span>';
                                    $statusBayarRaw = 'Belum Bayar';
                                    
                                    if ($latestSalary && ($latestSalary->payment_majikan_image || $latestSalary->payment_pembantu_image)) {
                                        $badges = [];
                                        $rawStatus = [];
                                        
                                        if ($latestSalary->payment_majikan_image) {
                                            $badges[] = '<span class="badge mb-1" style="background:#3498db; color:white; padding:4px 10px; border-radius:12px; font-size:11px; display:inline-block;">Lunas oleh Majikan</span>';
                                            $rawStatus[] = 'Lunas oleh Majikan';
                                        }
                                        
                                        if ($latestSalary->payment_pembantu_image) {
                                            $badges[] = '<span class="badge" style="background:#2ecc71; color:white; padding:4px 10px; border-radius:12px; font-size:11px; display:inline-block;">Lunas oleh Sipembantu</span>';
                                            $rawStatus[] = 'Lunas oleh Sipembantu';
                                        }
                                        
                                        if (count($badges) > 0) {
                                            $statusBayar = implode('<br>', $badges);
                                            $statusBayarRaw = implode(', ', $rawStatus);
                                        }
                                    }

                                    $statusKontrak = '<span class="text-muted small">-</span>';
                                    $statusKontrakRaw = 'Belum Diatur';
                                    if ($data->salary_type == 'contract') {
                                        $endDate = $data->work_end_date ? \Carbon\Carbon::parse($data->work_end_date) : $start->copy()->addMonths(12);
                                        if (now()->gt($endDate)) {
                                            $statusKontrak = '<span class="badge" style="background:#e74c3c; color:white; padding:4px 10px; border-radius:12px; font-size:12px;">Habis</span>';
                                            $statusKontrakRaw = 'Habis';
                                        } else {
                                            $statusKontrak = '<span class="badge" style="background:#2ecc71; color:white; padding:4px 10px; border-radius:12px; font-size:12px;">Aktif</span>';
                                            $statusKontrakRaw = 'Aktif';
                                        }
                                    }

                                    // Calculate Salaries based on Scheme
                                    $gajiPokok = $data->salary;
                                    $gajiPembantu = $gajiPokok;

                                    if ($data->scheme) {
                                        $mitraDeductions = 0;
                                        if (is_array($data->scheme->mitra_data)) {
                                            foreach ($data->scheme->mitra_data as $deduction) {
                                                if (isset($deduction['unit']) && $deduction['unit'] == '%') {
                                                    $mitraDeductions += ($gajiPokok * ($deduction['value'] / 100));
                                                } else {
                                                    $mitraDeductions += $deduction['value'];
                                                }
                                            }
                                        }
                                        $gajiPembantu -= $mitraDeductions;
                                    }
                                    
                                    $bankInfo = '<small class="text-danger">Belum ada Rekening</small>';
                                    if ($data->servant->servantDetails->is_bank == 1) {
                                        $bankInfo = '<small class="text-success"><i class="fas fa-university"></i> ' . $data->servant->servantDetails->bank_name . ' - ' . $data->servant->servantDetails->account_number . '</small>';
                                    }
                                    
                                    $bpjsInfo = '<small class="text-danger">Belum ada BPJS</small>';
                                    if ($data->servant->servantDetails->is_bpjs == 1) {
                                        $bpjsInfo = '<small class="text-success"><i class="fas fa-id-card"></i> ' . $data->servant->servantDetails->type_bpjs . ' - ' . $data->servant->servantDetails->number_bpjs . '</small>';
                                    }
                                @endphp
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td class="align-middle" style="border:none;">{{ $namaArt }}</td>
                                    <td class="align-middle" style="border:none;">{{ $namaMajikan }}</td>
                                    <td class="align-middle" style="border:none;">{!! $jenis !!}</td>
                                    <td class="align-middle" style="border:none;" data-sort="{{ $diffHari }}">{{ $lamaKerja }}</td>
                                    <td class="align-middle" style="border:none;">
                                        Rp {{ number_format($data->salary, 0, ',', '.') }}<br>
                                        <small class="text-muted">Potongan: Rp {{ number_format($gajiPembantu, 0, ',', '.') }}</small>
                                    </td>
                                    <td class="align-middle" style="border:none;">
                                        {!! $bankInfo !!}<br>
                                        {!! $bpjsInfo !!}
                                    </td>
                                    <td class="align-middle" style="border:none;">{!! $statusBayar !!}</td>
                                    <td class="align-middle" style="border:none;">{!! $statusKontrak !!}</td>
                                    <td class="align-middle" style="border:none;">
                                        <a href="{{ route('worker.show', $data->id) }}" class="btn btn-sm mb-1" style="background:#34495e; color:white; border-radius:5px; font-size:12px; padding:5px 10px;">Detail</a>
                                        
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-sm btn-light dropdown-toggle mb-1" type="button" id="dropdownMenuButton{{$data->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size:12px; border:1px solid #ddd; border-radius:5px;">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu shadow animated--fade-in" aria-labelledby="dropdownMenuButton{{$data->id}}">
                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editSchemaModal-{{ $data->id }}"><i class="fas fa-edit fa-fw mr-2 text-warning"></i> Pengaturan Skema</a>
                                                
                                                @if ($data->servant->servantDetails->is_bank == 0 || $data->servant->servantDetails->is_bpjs == 0)
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editBankModal-{{ $data->id }}"><i class="fas fa-money-check fa-fw mr-2 text-secondary"></i> Pengaturan Bank/BPJS</a>
                                                @endif
                                                
                                                @if ($data->status == 'review')
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#laidoffModal-{{ $data->id }}"><i class="fas fa-check fa-fw mr-2 text-success"></i> Selesaikan Pekerjaan</a>
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#rejectModal-{{ $data->id }}"><i class="fas fa-times fa-fw mr-2 text-danger"></i> Batalkan Pekerjaan</a>
                                                @endif
                                                
                                                @if ($data->status == 'accepted')
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#reviewModal-{{ $data->id }}"><i class="fas fa-user-times fa-fw mr-2 text-danger"></i> Berhenti (Review)</a>
                                                @endif
                                                @if ($data->status == 'accepted' || empty($data->salary_type))
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#salaryModal-{{ $data->id }}"><i class="fas fa-file-invoice-dollar fa-fw mr-2 text-primary"></i> Ubah Penggajian & Kontrak</a>
                                                @endif
                                            </div>
                                        </div>

                                        @include('cms.servant.modal.schema', ['data' => $data])
                                        @if ($data->servant->servantDetails->is_bank == 0 || $data->servant->servantDetails->is_bpjs == 0)
                                            @include('cms.servant.modal.edit-bank', ['data' => $data])
                                        @endif
                                        @if ($data->status == 'review')
                                            @include('cms.servant.modal.laidoff', ['data' => $data])
                                            @include('cms.servant.modal.reject', ['data' => $data])
                                        @endif
                                        @if ($data->status == 'accepted')
                                            @include('cms.servant.modal.review', ['data' => $data])
                                        @endif
                                    </td>
                                    
                                    <!-- Hidden cols for filtering datatable custom -->
                                    <td class="d-none col-jenis">{{ $jenisRaw }}</td>
                                    <td class="d-none col-tipefee">{{ $tipeFeeRaw }}</td>
                                    <td class="d-none col-lamakerja">{{ $lamaKerjaRaw }}</td>
                                    <td class="d-none col-statusbayar">{{ $statusBayarRaw }}</td>
                                    <td class="d-none col-statuskontrak">{{ $statusKontrakRaw }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <!-- EXISTING MAJIKAN AND PEMBANTU UI -->
        @hasrole('majikan')
            @php
                $totalPekerja = $datas->where('status', 'accepted')->count();
                $totalGajiBulanIni = 0;
                
                foreach($datas->where('status', 'accepted') as $data) {
                    $gajiPokok = $data->salary;
                    $gajiMajikan = $gajiPokok;
                    if ($data->scheme) {
                        $clientFees = 0;
                        if (is_array($data->scheme->client_data)) {
                            foreach ($data->scheme->client_data as $fee) {
                                if (isset($fee['unit']) && $fee['unit'] == '%') {
                                    $clientFees += ($gajiPokok * ($fee['value'] / 100));
                                } else {
                                    $clientFees += $fee['value'];
                                }
                            }
                        }
                        $gajiMajikan += $clientFees;
                    }
                    $totalGajiBulanIni += $gajiMajikan;
                }
            @endphp
            
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Pekerja Aktif</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPekerja }} Orang</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                        Total Pengeluaran Gaji / Bulan</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalGajiBulanIni, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wallet fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <button class="btn btn-secondary shadow-sm" data-toggle="modal" data-target="#historyMajikanModal">
                    <i class="fas fa-history mr-2"></i> Lihat Riwayat Pekerja
                </button>
            </div>

            <div class="row">
                @forelse($datas as $data)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                            <div class="card-body text-left">
                                {{-- Header: Name & Status --}}
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="text-truncate" style="max-width: 65%;">
                                        <h5 class="card-title font-weight-bold text-dark mb-0 text-truncate">{{ $data->servant->name }}</h5>
                                        <small class="text-muted font-weight-bold mt-1 d-block">
                                            @if($data->salary_type == 'contract')
                                                (Tipe Kontrak)
                                            @elseif($data->salary_type == 'fee')
                                                @php
                                                    $freqLabel = match($data->infal_frequency) {
                                                        'hourly' => 'Per Jam',
                                                        'daily' => 'Harian',
                                                        'weekly' => 'Mingguan',
                                                        'monthly' => 'Bulanan',
                                                        default => 'Bulanan'
                                                    };
                                                @endphp
                                                (Tipe Fee - {{ $freqLabel }})
                                            @else
                                                (Belum Diatur)
                                            @endif
                                        </small>
                                    </div>
                                    <span class="badge badge-{{ match ($data->status) {
                                            'accepted' => 'success',
                                            'rejected', 'laidoff' => 'danger',
                                            'pending' => 'warning',
                                            'interview', 'schedule', 'art_signed' => 'info',
                                            'verify', 'contract', 'validated' => 'success',
                                            'invited', 'contract_uploaded' => 'primary',
                                            default => 'secondary',
                                        } }} px-3 py-2" style="border-radius: 20px;">
                                        {{ match ($data->status) {
                                            'accepted' => 'Diterima',
                                            'rejected' => 'Ditolak',
                                            'laidoff' => 'Diberhentikan',
                                            'pending' => 'Pending',
                                            'schedule' => 'Penjadwalan',
                                            'interview' => 'Interview',
                                            'passed' => 'Lolos Interview',
                                            'choose' => 'Verifikasi',
                                            'verify' => 'Persiapan',
                                            'contract' => 'Perjanjian',
                                            'invited' => 'Validasi',
                                            'validated' => 'Tervalidasi',
                                            'art_signed' => 'TTD ART',
                                            'contract_uploaded' => 'Kontrak Diupload',
                                            default => 'Review',
                                        } }}
                                    </span>
                                </div>
                                
                                {{-- Core Details --}}
                                @php
                                    $gajiPokok = $data->salary;
                                    $gajiMajikan = $gajiPokok;
                                    if ($data->scheme) {
                                        $clientFees = 0;
                                        if (is_array($data->scheme->client_data)) {
                                            foreach ($data->scheme->client_data as $fee) {
                                                if (isset($fee['unit']) && $fee['unit'] == '%') {
                                                    $clientFees += ($gajiPokok * ($fee['value'] / 100));
                                                } else {
                                                    $clientFees += $fee['value'];
                                                }
                                            }
                                        }
                                        $gajiMajikan += $clientFees;
                                    }
                                @endphp
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-calendar-alt fa-fw mr-1 text-primary"></i> Mulai: {{ \Carbon\Carbon::parse($data->work_start_date)->format('d M Y') }}
                                </p>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-wallet fa-fw mr-1 text-success"></i> Gaji: Rp {{ number_format($gajiMajikan, 0, ',', '.') }}
                                </p>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-university fa-fw mr-1 text-info"></i> Bank: 
                                    @if ($data->servant && $data->servant->servantDetails && $data->servant->servantDetails->is_bank == 1)
                                        {{ $data->servant->servantDetails->bank_name }} ({{ $data->servant->servantDetails->account_number }})
                                    @else
                                        <span class="text-danger">Belum Diatur</span>
                                    @endif
                                </p>
                                
                                {{-- Highlight Box: Quota & Warranty --}}
                                <div class="bg-light p-3 rounded mb-3">
                                    {{-- Info Lowongan & Kuota --}}
                                    <div class="{{ $data->salary_type == 'contract' ? 'mb-2 pb-2 border-bottom' : '' }}">
                                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Info Lowongan</div>
                                        @if ($data->vacancy)
                                            @php
                                                $acceptedCount = $data->vacancy->applications()->where('status', 'accepted')->count();
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="font-weight-bold text-dark text-truncate pr-2" style="max-width: 60%;" title="{{ $data->vacancy->title }}">{{ $data->vacancy->title }}</span>
                                                <span class="badge badge-{{ $acceptedCount >= $data->vacancy->limit ? 'success' : 'warning' }}">
                                                    Terisi: {{ $acceptedCount }}/{{ $data->vacancy->limit }}
                                                </span>
                                            </div>
                                            <div class="text-muted small d-flex justify-content-between">
                                                <span>Aksi Lowongan:</span>
                                                <a href="#" class="text-primary" data-toggle="modal" data-target="#riwayatModal-{{ $data->id }}">
                                                    Lihat Riwayat Pelamar <i class="fas fa-arrow-right ml-1"></i>
                                                </a>
                                                @include('cms.servant.modal.riwayat', ['data' => $data])
                                            </div>
                                        @else
                                            <span class="text-dark font-weight-bold">-</span>
                                        @endif
                                    </div>
                                    
                                    {{-- Info Garansi --}}
                                    @if($data->salary_type == 'contract')
                                        <div>
                                            <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Masa Garansi</div>
                                            @php
                                                $startKontrak = $data->work_start_date ? \Carbon\Carbon::parse($data->work_start_date) : null;
                                                $garansiDuration = 3; 
                                                $garansiEnd = $startKontrak ? $startKontrak->copy()->addMonths($garansiDuration) : null;
                                                
                                                $sisaGaransiHtml = '<span class="text-danger font-weight-bold">Garansi Habis</span>';
                                                if ($garansiEnd && now()->lessThan($garansiEnd)) {
                                                    $sisaBulan = (int) now()->diffInMonths($garansiEnd);
                                                    $sisaHari = (int) now()->copy()->addMonths($sisaBulan)->diffInDays($garansiEnd);
                                                    
                                                    $textGaransi = '';
                                                    if($sisaBulan > 0) $textGaransi .= $sisaBulan . ' Bulan ';
                                                    if($sisaHari > 0) $textGaransi .= $sisaHari . ' Hari';
                                                    if(empty($textGaransi)) $textGaransi = '< 1 Hari';
                                                    
                                                    $sisaGaransiHtml = '<span class="text-success font-weight-bold">Aktif (Sisa ' . $textGaransi . ')</span>';
                                                }
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center">
                                                {!! $sisaGaransiHtml !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions Footer --}}
                            <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                                <div class="row mx-0">
                                    <div class="col-6 pl-0 pr-1">
                                        <a href="{{ route('worker.show', $data->id) }}" class="btn btn-info btn-block btn-sm mb-2" style="border-radius: 8px;">
                                            <i class="fas fa-fw fa-eye mr-1"></i> Detail
                                        </a>
                                    </div>
                                    <div class="col-6 pr-0 pl-1">
                                        <a href="#" class="btn btn-success btn-block btn-sm mb-2" style="border-radius: 8px;" data-toggle="modal" data-target="#kontrakSayaModal-{{ $data->id }}">
                                            <i class="fas fa-fw fa-file-signature mr-1"></i> Kontrak
                                        </a>
                                        @include('cms.servant.modal.kontrak-saya', ['data' => $data])
                                    </div>
                                    @if (!$data->pengaduan->contains('contract_id', $data->id) || !$data->pengaduan->contains('reporter_id', auth()->user()->id))
                                        <div class="col-12 px-0">
                                            <a href="#" class="btn btn-outline-danger btn-block btn-sm" style="border-radius: 8px;" data-toggle="modal" data-target="#complaintModal-{{ $data->id }}">
                                                <i class="fas fa-fw fa-bullhorn mr-1"></i> Buat Pengaduan
                                            </a>
                                            @include('cms.servant.modal.complaint', ['data' => $data])
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <img src="{{ asset('assets/img/undraw_empty.svg') }}" alt="Kosong" style="width: 150px; opacity: 0.5;" class="mb-3">
                        <p class="text-muted">Belum ada pekerja aktif.</p>
                    </div>
                @endforelse
            </div>
            
            {{-- History Modal for Majikan --}}
            <div class="modal fade" id="historyMajikanModal" tabindex="-1" aria-labelledby="historyMajikanModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold" id="historyMajikanModalLabel"><i class="fas fa-history mr-2"></i> Riwayat Pekerja</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Nama ART</th>
                                            <th>Tipe Pekerjaan</th>
                                            <th>Masa Kerja</th>
                                            <th>Status Berhenti</th>
                                            <th style="min-width: 250px;">Rating & Ulasan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($historyDatas ?? [] as $history)
                                            @php
                                                $start = \Carbon\Carbon::parse($history->work_start_date);
                                                $end = $history->work_end_date ? \Carbon\Carbon::parse($history->work_end_date) : $history->updated_at;
                                                
                                                // Get the review where Majikan is the reviewer AND ART is the reviewee (or vice versa? UI usually shows what Majikan gave or both)
                                                // Let's show the review Majikan gave to the ART.
                                                $myReview = clone $history->reviews;
                                                $reviewToArt = $myReview->where('reviewer_id', auth()->id())->first();
                                                $reviewFromArt = $myReview->where('reviewee_id', auth()->id())->first();
                                            @endphp
                                            <tr>
                                                <td class="align-middle font-weight-bold">{{ $history->servant->name }}</td>
                                                <td class="align-middle">
                                                    @if($history->salary_type == 'contract')
                                                        <span class="badge badge-primary">Kontrak</span>
                                                    @elseif($history->salary_type == 'fee')
                                                        <span class="badge badge-success">Fee</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    {{ $start->format('d M Y') }} s/d<br>
                                                    {{ $end->format('d M Y') }}
                                                </td>
                                                <td class="align-middle">
                                                    @php
                                                        $endReasonMap = [
                                                            'selesai_kontrak' => ['label' => 'Selesai Kontrak', 'color' => '#28a745'],
                                                            'diberhentikan' => ['label' => 'Diberhentikan', 'color' => '#dc3545'],
                                                            'diganti' => ['label' => 'Diganti', 'color' => '#ffc107'],
                                                            'mengundurkan_diri' => ['label' => 'Mengundurkan Diri', 'color' => '#007bff'],
                                                        ];
                                                        $reason = $history->end_reason ?? null;
                                                        $badgeInfo = $reason && isset($endReasonMap[$reason]) 
                                                            ? $endReasonMap[$reason] 
                                                            : ['label' => ($history->status == 'laidoff' ? 'Diberhentikan / Diganti' : 'Dibatalkan'), 'color' => '#6c757d'];
                                                        $textColor = ($reason == 'diganti') ? '#212529' : '#ffffff';
                                                    @endphp
                                                    <span class="badge" style="background-color: {{ $badgeInfo['color'] }}; color: {{ $textColor }}; padding: 5px 10px; border-radius: 12px; font-size: 12px;">
                                                        {{ $badgeInfo['label'] }}
                                                    </span>
                                                    <div class="mt-2 text-center">
                                                        <a href="{{ route('worker.show', $history->id) }}" class="btn btn-sm btn-outline-info w-100" style="font-size: 11px;">
                                                            <i class="fas fa-eye"></i> Detail Kontrak / Ulasan
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($reviewToArt)
                                                        <div class="mb-2 p-2 border rounded border-warning bg-white">
                                                            <small class="font-weight-bold text-muted d-block mb-1">Ulasan Anda untuk ART:</small>
                                                            <div class="mb-1">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="fas fa-star {{ $i <= $reviewToArt->rating ? 'text-warning' : 'text-body' }}" style="font-size:12px;"></i>
                                                                @endfor
                                                            </div>
                                                            <p class="mb-0 text-dark small">"{{ $reviewToArt->comment }}"</p>
                                                        </div>
                                                    @else
                                                        <small class="text-muted d-block mb-2"><i class="fas fa-info-circle"></i> Anda belum memberi ulasan</small>
                                                    @endif
                                                    
                                                    @if($reviewFromArt)
                                                        <div class="p-2 border rounded bg-light">
                                                            <small class="font-weight-bold text-muted d-block mb-1">Ulasan ART untuk Anda:</small>
                                                            <div class="mb-1">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="fas fa-star {{ $i <= $reviewFromArt->rating ? 'text-warning' : 'text-body' }}" style="font-size:12px;"></i>
                                                                @endfor
                                                            </div>
                                                            <p class="mb-0 text-dark small">"{{ $reviewFromArt->comment }}"</p>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat pekerja.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            
        @endhasrole

        @hasrole('pembantu')
            <div class="mb-3 text-right">
                <button class="btn btn-secondary shadow-sm" data-toggle="modal" data-target="#historyPembantuModal">
                    <i class="fas fa-history mr-2"></i> Lihat Riwayat Pekerjaan
                </button>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama Majikan</th>
                                    <th>Gaji Pokok</th>
                                    <th>Tanggal Bekerja</th>
                                    <th>Gaji (Dengan Potongan)</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $data)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">
                                            @if ($data->vacancy_id != null && $data->vacancy && $data->vacancy->user)
                                                {{ $data->vacancy->user->name }}
                                            @elseif($data->employe)
                                                {{ $data->employe->name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">Rp. {{ number_format($data->salary, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($data->work_start_date)->format('d-M-Y') }}</td>
                                        
                                        @php
                                            $gajiPokok = $data->salary;
                                            $gajiPembantu = $gajiPokok;

                                            if ($data->scheme) {
                                                $mitraDeductions = 0;
                                                if (is_array($data->scheme->mitra_data)) {
                                                    foreach ($data->scheme->mitra_data as $deduction) {
                                                        if (isset($deduction['unit']) && $deduction['unit'] == '%') {
                                                            $mitraDeductions += ($gajiPokok * ($deduction['value'] / 100));
                                                        } else {
                                                            $mitraDeductions += $deduction['value'];
                                                        }
                                                    }
                                                }
                                                $gajiPembantu -= $mitraDeductions;
                                            }
                                        @endphp
                                        <td class="text-center">Rp. {{ number_format($gajiPembantu, 0, ',', '.') }}</td>
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
                                                    'invited' => 'primary',
                                                    'validated' => 'success',
                                                    'art_signed' => 'info',
                                                    'contract_uploaded' => 'primary',
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
                                                    'invited' => 'Undangan Validasi',
                                                    'validated' => 'Tervalidasi',
                                                    'art_signed' => 'TTD ART',
                                                    'contract_uploaded' => 'Kontrak Diupload',
                                                    default => 'Review',
                                                } }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('worker.show', $data->id) }}" class="btn btn-sm btn-info mb-1"><i class="fas fa-eye"></i></a>

                                            @if (!$data->pengaduan->contains('contract_id', $data->id) || !$data->pengaduan->contains('reporter_id', auth()->user()->id))
                                                <a href="#" class="btn btn-sm btn-danger mb-1" data-toggle="modal" data-target="#complaintModal-{{ $data->id }}">
                                                    <i class="fas fa-bullhorn"></i>
                                                </a>
                                                @include('cms.servant.modal.complaint', ['data' => $data])
                                            @endif

                                            @if ($data->status === 'invited')
                                                <a href="#" class="btn btn-sm btn-primary mb-1" data-toggle="modal" data-target="#validationInfoModal-{{ $data->id }}" title="Lihat Undangan Validasi">
                                                    <i class="fas fa-envelope-open-text"></i>
                                                </a>
                                                @include('cms.applicant.modal.validation-info', ['data' => $data])
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            {{-- History Modal for Pembantu --}}
            <div class="modal fade" id="historyPembantuModal" tabindex="-1" aria-labelledby="historyPembantuModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold" id="historyPembantuModalLabel"><i class="fas fa-history mr-2"></i> Riwayat Pekerjaan Saya</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Nama Majikan</th>
                                            <th>Tipe Pekerjaan</th>
                                            <th>Masa Kerja</th>
                                            <th>Status Berhenti</th>
                                            <th style="min-width: 250px;">Rating & Ulasan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($historyDatas ?? [] as $history)
                                            @php
                                                $start = \Carbon\Carbon::parse($history->work_start_date);
                                                $end = $history->work_end_date ? \Carbon\Carbon::parse($history->work_end_date) : $history->updated_at;
                                                
                                                $namaMajikan = $history->vacancy_id != null && $history->vacancy && $history->vacancy->user ? $history->vacancy->user->name : ($history->employe ? $history->employe->name : '-');
                                                
                                                // Get the reviews
                                                $myReview = clone $history->reviews;
                                                $reviewToMajikan = $myReview->where('reviewer_id', auth()->id())->first();
                                                $reviewFromMajikan = $myReview->where('reviewee_id', auth()->id())->first();
                                            @endphp
                                            <tr>
                                                <td class="align-middle font-weight-bold">{{ $namaMajikan }}</td>
                                                <td class="align-middle">
                                                    @if($history->salary_type == 'contract')
                                                        <span class="badge badge-primary">Kontrak</span>
                                                    @elseif($history->salary_type == 'fee')
                                                        <span class="badge badge-success">Fee</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    {{ $start->format('d M Y') }} s/d<br>
                                                    {{ $end->format('d M Y') }}
                                                </td>
                                                <td class="align-middle">
                                                    @php
                                                        $endReasonMap = [
                                                            'selesai_kontrak' => ['label' => 'Selesai Kontrak', 'color' => '#28a745'],
                                                            'diberhentikan' => ['label' => 'Diberhentikan', 'color' => '#dc3545'],
                                                            'diganti' => ['label' => 'Diganti', 'color' => '#ffc107'],
                                                            'mengundurkan_diri' => ['label' => 'Mengundurkan Diri', 'color' => '#007bff'],
                                                        ];
                                                        $reason = $history->end_reason ?? null;
                                                        $badgeInfo = $reason && isset($endReasonMap[$reason]) 
                                                            ? $endReasonMap[$reason] 
                                                            : ['label' => ($history->status == 'laidoff' ? 'Diberhentikan / Diganti' : 'Dibatalkan'), 'color' => '#6c757d'];
                                                        $textColor = ($reason == 'diganti') ? '#212529' : '#ffffff';
                                                    @endphp
                                                    <span class="badge" style="background-color: {{ $badgeInfo['color'] }}; color: {{ $textColor }}; padding: 5px 10px; border-radius: 12px; font-size: 12px;">
                                                        {{ $badgeInfo['label'] }}
                                                    </span>
                                                    <div class="mt-2 text-center">
                                                        <a href="{{ route('worker.show', $history->id) }}" class="btn btn-sm btn-outline-info w-100" style="font-size: 11px;">
                                                            <i class="fas fa-eye"></i> Detail Kontrak / Ulasan
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($reviewToMajikan)
                                                        <div class="mb-2 p-2 border rounded border-warning bg-white">
                                                            <small class="font-weight-bold text-muted d-block mb-1">Ulasan Anda untuk Majikan:</small>
                                                            <div class="mb-1">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="fas fa-star {{ $i <= $reviewToMajikan->rating ? 'text-warning' : 'text-body' }}" style="font-size:12px;"></i>
                                                                @endfor
                                                            </div>
                                                            <p class="mb-0 text-dark small">"{{ $reviewToMajikan->comment }}"</p>
                                                        </div>
                                                    @else
                                                        <small class="text-muted d-block mb-2"><i class="fas fa-info-circle"></i> Anda belum memberi ulasan</small>
                                                    @endif
                                                    
                                                    @if($reviewFromMajikan)
                                                        <div class="p-2 border rounded bg-light">
                                                            <small class="font-weight-bold text-muted d-block mb-1">Ulasan Majikan untuk Anda:</small>
                                                            <div class="mb-1">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="fas fa-star {{ $i <= $reviewFromMajikan->rating ? 'text-warning' : 'text-body' }}" style="font-size:12px;"></i>
                                                                @endfor
                                                            </div>
                                                            <p class="mb-0 text-dark small">"{{ $reviewFromMajikan->comment }}"</p>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat pekerjaan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            
        @endhasrole
    @endif

    <!-- External Modals for Admin Table (Outside DataTables DOM to prevent click issues) -->
    @hasrole('superadmin|admin|owner')
        @foreach ($datas as $data)
            @if ($data->status == 'accepted' || empty($data->salary_type))
                @include('cms.applicant.modal.salary', ['data' => $data, 'schemeOptions' => $schemas ?? collect()])
            @endif
        @endforeach
    @endhasrole
    
@endsection

@push('custom-script')
<script>
$(document).ready(function() {
    // Only init if table belongs to admin
    if ($('#dataTableAdmin').length) {
        var table = $('#dataTableAdmin').DataTable({
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "pageLength": 10,
            "ordering": false // Custom aesthetic standard, remove ordering arrows on headers initially to match html snippet. 
        });

        // Custom filtering logic
        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                if(settings.nTable.id !== 'dataTableAdmin') {
                    return true;
                }

                var filterJenis = $('#filterJenis').val();
                var filterTipeFee = $('#filterTipeFee').val();
                var filterLamaKerja = $('#filterLamaKerja').val();
                var filterStatusBayar = $('#filterStatusBayar').val();
                var filterStatusKontrak = $('#filterStatusKontrak').val();
                var filterNama = $('#filterNama').val().toLowerCase();

                var namaArt = data[0] || '';
                var majikan = data[1] || '';
                var colJenis = data[9] || ''; // Data from hidden columns
                var colTipeFee = data[10] || '';
                var colLamaKerja = data[11] || '';
                var colStatusBayar = data[12] || '';
                var colStatusKontrak = data[13] || '';

                if (filterJenis && colJenis !== filterJenis) return false;
                if (filterTipeFee && colTipeFee !== filterTipeFee) return false;
                if (filterLamaKerja && colLamaKerja !== filterLamaKerja) return false;
                if (filterStatusBayar && colStatusBayar.indexOf(filterStatusBayar) === -1) return false;
                if (filterStatusKontrak && colStatusKontrak !== filterStatusKontrak) return false;
                
                if (filterNama) {
                    if (namaArt.toLowerCase().indexOf(filterNama) === -1 && majikan.toLowerCase().indexOf(filterNama) === -1) {
                        return false;
                    }
                }

                return true;
            }
        );

        $('#btnApplyFilter').on('click', function() {
            table.draw();
        });

        $('#btnResetFilter').on('click', function() {
            $('#filterJenis').val('');
            $('#filterTipeFee').val('');
            $('#filterLamaKerja').val('');
            $('#filterStatusBayar').val('');
            $('#filterStatusKontrak').val('');
            $('#filterNama').val('');
            table.draw();
        });
    }

    // Global Event Delegation for Salary Modals
    // 1. Handle "Jenis Penggajian" Radio Change
    $(document).on('change', '.salary-type-radio', function() {
        var $this = $(this);
        var $modal = $this.closest('.modal'); // Find the specific modal instance
        var targetId = $this.data('target');

        // Hide all salary-sections IN THIS MODAL only
        $modal.find('.salary-section').hide();
        
        // Show the target section
        if(targetId) {
            $(targetId).show();
        }

        // If "Fee" is selected, trigger the infal switch to set correct state
        if ($this.val() === 'fee') {
            // Find the switch in this modal and trigger change
            $modal.find('.is-infal-switch').trigger('change');
        }

        // Recalculate Scheme
        var $schemeSelect = $modal.find('.scheme-select');
        if ($schemeSelect.length > 0 && $schemeSelect.val()) {
            showSchemeDetail($schemeSelect[0]);
        }
    });

    // 2. Handle "Mode Infal" Switch Change
    $(document).on('change', '.is-infal-switch', function() {
        var $this = $(this);
        var $modal = $this.closest('.modal');
        var isInfal = $this.is(':checked');
        var targetInfal = $this.data('target');
        var targetRegular = $this.data('regular');

        if (isInfal) {
            $(targetInfal).show();
            $(targetRegular).hide();
        } else {
            $(targetInfal).hide();
            $(targetRegular).show();
        }

        // Recalculate Scheme
        var $schemeSelect = $modal.find('.scheme-select');
        if ($schemeSelect.length > 0 && $schemeSelect.val()) {
            showSchemeDetail($schemeSelect[0]);
        }
    });

    // 3. Initialize Correct State when Modal Opens
    $(document).on('shown.bs.modal', '.modal', function () {
        var $modal = $(this);
        // Check if this is a salary modal (has salary-type-radio)
        var $radios = $modal.find('.salary-type-radio');
        if ($radios.length > 0) {
                var $checked = $modal.find('.salary-type-radio:checked');
                if ($checked.length > 0) {
                    $checked.trigger('change');
                } else {
                    // Clean state if nothing checked
                    $modal.find('.salary-section').hide();
                }
        }
        
        var $schemeSelect = $modal.find('.scheme-select');
        if ($schemeSelect.length > 0 && $schemeSelect.val()) {
            showSchemeDetail($schemeSelect[0]);
        }
    });

    // ======================================
    // 4. Rupiah Input Formatting (titik ribuan)
    // ======================================

    function formatRupiahInput(angka) {
        var numberStr = angka.toString().replace(/[^0-9]/g, '');
        if (numberStr === '') return '';
        return numberStr.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseRupiahInput(formatted) {
        return parseInt(formatted.replace(/\./g, '')) || 0;
    }

    // Event: Format input saat user mengetik
    $(document).on('input', '.rupiah-input', function() {
        var $this = $(this);
        var cursorPos = this.selectionStart;
        var oldVal = $this.val();
        var oldLen = oldVal.length;

        // Ambil angka mentah, format ulang
        var rawNumber = parseRupiahInput(oldVal);
        var formatted = rawNumber > 0 ? formatRupiahInput(rawNumber) : '';
        
        $this.val(formatted);

        // Update hidden input
        var targetName = $this.data('target');
        if (targetName) {
            $this.siblings('input[name="' + targetName + '"]').val(rawNumber > 0 ? rawNumber : '');
        }

        // Adjust cursor position
        var newLen = formatted.length;
        var newPos = cursorPos + (newLen - oldLen);
        this.setSelectionRange(newPos, newPos);

        // Recalculate Scheme jika ada
        var $modal = $this.closest('.modal');
        var $schemeSelect = $modal.find('.scheme-select');
        if ($schemeSelect.length > 0 && $schemeSelect.val()) {
            showSchemeDetail($schemeSelect[0]);
        }
    });
});

// Global function for scheme detail display
function showSchemeDetail(selectEl) {
    var $select = $(selectEl);
    var modalId = $select.data('modal-id');
    var $summary = $('#schemeSummary-' + modalId);
    var $option = $select.find(':selected');
    var $modal = $select.closest('.modal');

    if (!$select.val()) {
        $summary.slideUp(200);
        return;
    }

    function fmtVal(item) {
        if (item.unit === 'Rp') {
            var num = parseFloat(item.value) || 0;
            return 'Rp ' + num.toLocaleString('id-ID');
        }
        return item.value + '%';
    }

    // Format angka ke format Indonesia (titik sebagai pemisah ribuan)
    function formatRupiah(angka) {
        var number = Math.round(angka);
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Ambil nominal gaji aktif dari modal
    function getActiveSalary($modal) {
        var salary = 0;
        var salaryType = $modal.find('.salary-type-radio:checked').val();

        if (salaryType === 'contract') {
            salary = parseFloat($modal.find('input[name="contract_salary"]').val()) || 0;
        } else if (salaryType === 'fee') {
            var isInfal = $modal.find('.is-infal-switch').is(':checked');
            if (isInfal) {
                salary = parseFloat($modal.find('input[name="fee_salary_infal"]').val()) || 0;
            } else {
                salary = parseFloat($modal.find('input[name="fee_salary_regular"]').val()) || 0;
            }
        }

        return salary;
    }

    try {
        var clientData = JSON.parse($option.attr('data-client')) || [];
        var mitraData = JSON.parse($option.attr('data-mitra')) || [];

        var clientHtml = clientData.map(function(f) {
            return '<span class="badge badge-light border mr-1">' + f.label + ': <strong>' + fmtVal(f) + '</strong></span>';
        }).join(' ');

        var mitraHtml = mitraData.map(function(f) {
            return '<span class="badge badge-light border mr-1">' + f.label + ': <strong>' + fmtVal(f) + '</strong></span>';
        }).join(' ');

        $summary.find('.scheme-client-detail').html(clientHtml);
        $summary.find('.scheme-mitra-detail').html(mitraHtml);

        // Hitung Total Tagihan Majikan & Estimasi Gaji Bersih Pekerja
        var gajiPokok = getActiveSalary($modal);
        
        // Hitung total biaya klien (ditanggung majikan)
        var totalClientExtra = 0;
        clientData.forEach(function(item) {
            var val = parseFloat(item.value) || 0;
            if (item.unit === '%') {
                totalClientExtra += (gajiPokok * val / 100);
            } else {
                totalClientExtra += val;
            }
        });

        // Hitung total potongan mitra (dipotong dari gaji pekerja)
        var totalMitraDeduction = 0;
        mitraData.forEach(function(item) {
            var val = parseFloat(item.value) || 0;
            if (item.unit === '%') {
                totalMitraDeduction += (gajiPokok * val / 100);
            } else {
                totalMitraDeduction += val;
            }
        });

        var totalTagihanMajikan = gajiPokok + totalClientExtra;
        var gajiBersihPekerja = gajiPokok - totalMitraDeduction;

        $summary.find('.scheme-employer-total').text(formatRupiah(totalTagihanMajikan));
        $summary.find('.scheme-worker-net').text(formatRupiah(Math.max(0, gajiBersihPekerja)));

        $summary.slideDown(200);
    } catch(e) {
        $summary.slideUp(200);
    }
}
</script>
@endpush
