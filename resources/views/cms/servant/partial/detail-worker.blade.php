@extends('cms.layouts.main', ['title' => 'Detail Pekerja'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <h1 class="h3 mb-4 text-gray-800">Detail Pekerja</h1>
        <div class="d-flex">
            <a href="{{ route('worker-all') }}" class="btn btn-sm btn-secondary shadow"><i
                    class="fas fa-fw fa-arrow-left"></i></a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                {{-- Worker Info Header (compact, horizontal) --}}
                <div class="card-header py-3 bg-white border-bottom">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-3">
                            @if ($data->servant && $data->servant->servantDetails && $data->servant->servantDetails->photo)
                                <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $data->servant->servantDetails->photo]) }}"
                                    class="rounded-circle zoomable-image" style="width: 95px; height: 95px; object-fit: cover;"
                                    alt="Foto Pekerja">
                            @else
                                <img src="{{ asset('assets/img/undraw_rocket.svg') }}"
                                    class="rounded-circle" style="width: 95px; height: 95px; object-fit: cover;"
                                    alt="Foto Pekerja">
                            @endif
                        </div>
                        <div class="mr-4">
                            <h5 class="font-weight-bold text-dark mb-0">{{ $data->servant ? $data->servant->name : '-' }}</h5>
                            <small class="text-muted">
                                <i class="fas fa-user-tie mr-1"></i>
                                @if ($data->vacancy_id != null && $data->vacancy && $data->vacancy->user)
                                    {{ $data->vacancy->user->name }}
                                @elseif($data->employe)
                                    {{ $data->employe->name }}
                                @else
                                    -
                                @endif
                            </small>
                        </div>
                        <div class="d-flex flex-wrap" style="gap: 15px;">
                            <div>
                                <small class="text-muted d-block"><i class="fas fa-clock mr-1"></i> Mulai Kerja</small>
                                <span class="font-weight-bold text-dark">{{ \Carbon\Carbon::parse($data->work_start_date)->format('d F Y') }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block"><i class="fas fa-money-bill-wave mr-1"></i> Gaji</small>
                                <span class="font-weight-bold text-dark">
                                    Rp. {{ number_format($data->salary, 0, ',', '.') }}
                                    @if($data->is_infal && $data->infal_frequency)
                                        <small class="text-muted">
                                            / {{ match($data->infal_frequency) { 'hourly' => 'Jam', 'daily' => 'Hari', 'weekly' => 'Minggu', 'monthly' => 'Bulan', default => '-' } }}
                                        </small>
                                    @elseif($data->salary_type == 'contract')
                                        <small class="text-muted">/ Bulan</small>
                                    @endif
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block"><i class="fas fa-tags mr-1"></i> Tipe</small>
                                <span class="font-weight-bold text-dark">
                                    @if($data->salary_type == 'contract')
                                        <span class="badge badge-primary px-2 py-1" style="border-radius:12px; font-size: 0.85rem;"><i class="fas fa-file-contract mr-1"></i> Kontrak</span>
                                    @elseif($data->is_infal && $data->infal_frequency)
                                        <span class="badge badge-info px-2 py-1" style="border-radius:12px; font-size: 0.85rem;"><i class="fas fa-clock mr-1"></i> Infal {{ match($data->infal_frequency) { 'hourly' => 'Per Jam', 'daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', default => '' } }}</span>
                                    @else
                                        <span class="badge badge-warning px-2 py-1" style="border-radius:12px; font-size: 0.85rem;"><i class="fas fa-hand-holding-usd mr-1"></i> Fee</span>
                                    @endif
                                </span>
                            </div>
                            @if($data->is_infal && $data->infal_frequency == 'hourly' && $data->infal_time_in && $data->infal_time_out)
                            <div>
                                <small class="text-muted d-block"><i class="fas fa-business-time mr-1"></i> Jam Kerja</small>
                                <span class="font-weight-bold text-dark">{{ $data->infal_time_in }} - {{ $data->infal_time_out }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tabs Navigation --}}
                <div class="card-header py-2 bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="workerTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold" id="gaji-tab" data-toggle="tab" href="#gaji" role="tab" aria-controls="gaji" aria-selected="true"><i class="fas fa-money-bill-wave mr-1"></i> Kehadiran & Gaji</a>
                        </li>
                        @hasrole('majikan|admin|superadmin')
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" id="file-kontrak-tab" data-toggle="tab" href="#file-kontrak" role="tab" aria-controls="file-kontrak" aria-selected="false"><i class="fas fa-file-contract mr-1"></i> File Kontrak</a>
                        </li>
                        @endhasrole
                        <li class="nav-item">
                             <a class="nav-link font-weight-bold" id="status-tab" data-toggle="tab" href="#status" role="tab" aria-controls="status" aria-selected="false"><i class="fas fa-info-circle mr-1"></i> Detail Pekerjaan</a>
                        </li>
                        @if($data->salary_type == 'contract' && $data->garansi_id)
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" id="garansi-tab" data-toggle="tab" href="#garansi" role="tab" aria-controls="garansi" aria-selected="false"><i class="fas fa-shield-alt mr-1"></i> Tagihan Garansi</a>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="workerTabsContent">
                        
                        {{-- TAB 2: FILE KONTRAK --}}
                        <div class="tab-pane fade" id="file-kontrak" role="tabpanel" aria-labelledby="file-kontrak-tab">
                            {{-- Contract Information Section --}}
                            <div class="mb-3 d-flex justify-content-between align-items-center border-bottom pb-2">
                                <h5 class="font-weight-bold mb-0 text-dark"><i class="fas fa-file-contract mr-1"></i> Informasi Kontrak & File</h5>
                                @hasrole('majikan|admin|superadmin')
                                    <a href="#" class="btn btn-sm btn-primary" data-toggle="modal"
                                        data-target="#uploadContractModal-{{ $data->id }}">
                                        <i class="fas fa-upload mr-1"></i>
                                        {{ $data->file_contract ? 'Ganti Kontrak' : 'Upload Kontrak' }}
                                    </a>
                                @endhasrole
                            </div>
                            <div class="pt-2">
                    {{-- Validation Timeline --}}
                    @if ($data->validation_date)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1"><i class="fas fa-calendar-check mr-1"></i> <b>Validasi:</b></small>
                            <small>
                                <i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($data->validation_date)->format('d F Y') }}<br>
                                <i class="fas fa-map-marker-alt mr-1"></i> {{ $data->validation_location ?? '-' }}
                                @if ($data->validated_at)
                                    <br><span class="badge badge-success"><i class="fas fa-check mr-1"></i> Tervalidasi {{ $data->validated_at->format('d M Y') }}</span>
                                @endif
                            </small>
                            @if ($data->validation_notes)
                                <br><small class="text-muted">Catatan: {{ $data->validation_notes }}</small>
                            @endif
                        </div>
                        <hr>
                    @endif

                    {{-- ART Signature --}}
                    @if ($data->art_signature_file)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1"><i class="fas fa-signature mr-1"></i> <b>TTD ART:</b></small>
                            @php
                                $artSigPath = storage_path('app/public/' . $data->art_signature_file);
                            @endphp
                            @if (file_exists($artSigPath))
                                @if (Str::endsWith($data->art_signature_file, ['.jpg', '.jpeg', '.png', '.gif']))
                                    <img src="{{ route('getFile', ['path' => dirname($data->art_signature_file), 'fileName' => basename($data->art_signature_file)]) }}"
                                        alt="TTD ART" class="img-fluid rounded zoomable-image" style="max-height: 120px;">
                                @elseif (Str::endsWith($data->art_signature_file, ['.pdf']))
                                    <a href="{{ route('getFile', ['path' => dirname($data->art_signature_file), 'fileName' => basename($data->art_signature_file)]) }}" 
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf mr-1"></i> Lihat PDF
                                    </a>
                                @endif
                            @else
                                <small class="text-danger">File tidak ditemukan</small>
                            @endif
                            @if ($data->art_signed_at)
                                <br><small class="text-muted">Ditandatangani: {{ $data->art_signed_at->format('d M Y H:i') }}</small>
                            @endif
                        </div>
                        <hr>
                    @endif

                    {{-- Employer Signature --}}
                    @if ($data->employer_signature_file)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1"><i class="fas fa-file-signature mr-1"></i> <b>Kontrak Majikan:</b></small>
                            @php
                                $empSigPath = storage_path('app/public/' . $data->employer_signature_file);
                            @endphp
                            @if (file_exists($empSigPath))
                                @if (Str::endsWith($data->employer_signature_file, ['.jpg', '.jpeg', '.png', '.gif']))
                                    <img src="{{ route('getFile', ['path' => dirname($data->employer_signature_file), 'fileName' => basename($data->employer_signature_file)]) }}"
                                        alt="Kontrak Majikan" class="img-fluid rounded zoomable-image" style="max-height: 120px;">
                                @elseif (Str::endsWith($data->employer_signature_file, ['.pdf']))
                                    <a href="{{ route('getFile', ['path' => dirname($data->employer_signature_file), 'fileName' => basename($data->employer_signature_file)]) }}" 
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf mr-1"></i> Lihat PDF
                                    </a>
                                @endif
                            @else
                                <small class="text-danger">File tidak ditemukan</small>
                            @endif
                        </div>
                        <hr>
                    @endif

                    {{-- Original Contract File --}}
                    @if ($data->file_contract)
                        <div class="mb-2">
                            <small class="text-muted d-block mb-1"><i class="fas fa-file-alt mr-1"></i> <b>File Kontrak:</b></small>
                            @php
                                $contractPath = storage_path('app/public/' . $data->file_contract);
                            @endphp
                            @if (file_exists($contractPath))
                                @if (Str::endsWith($data->file_contract, ['.jpg', '.jpeg', '.png', '.gif']))
                                    <img src="{{ route('getFile', ['path' => dirname($data->file_contract), 'fileName' => basename($data->file_contract)]) }}"
                                        alt="Kontrak" class="img-fluid rounded zoomable-image mb-2" style="max-height: 200px;">
                                @elseif (Str::endsWith($data->file_contract, ['.pdf']))
                                    <a href="{{ route('getFile', ['path' => dirname($data->file_contract), 'fileName' => basename($data->file_contract)]) }}" 
                                        target="_blank" class="btn btn-sm btn-outline-info mb-2">
                                        <i class="fas fa-file-pdf mr-1"></i> Lihat PDF
                                    </a>
                                @endif
                            @endif
                            <br>
                            <a href="{{ route('contract.download', ['applicationId' => $data->id]) }}" 
                                class="btn btn-sm btn-outline-success">
                                <i class="fas fa-download mr-1"></i> Download Kontrak
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-file-upload fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0"><small>Belum ada file kontrak yang diunggah.</small></p>
                        </div>
                    @endif
                </div>

            {{-- ADMIN DETAIL KONTRAK (dipindah ke sini, di bawah Download Kontrak) --}}
            @hasrole('superadmin|admin')
                @if($data->salary_type == 'contract')
                    @php
                        $startKontrak = $data->work_start_date ? \Carbon\Carbon::parse($data->work_start_date) : null;
                        $endKontrak = $data->work_end_date ? \Carbon\Carbon::parse($data->work_end_date) : ($startKontrak ? $startKontrak->copy()->addMonths(12) : null);
                        $durasiKontrak = $startKontrak && $endKontrak ? round($startKontrak->floatDiffInMonths($endKontrak)) : 12;
                        $statusKontrakHtml = '<span class="badge badge-success px-2 py-1" style="border-radius:12px">Aktif</span>';
                        if ($endKontrak && now()->gt($endKontrak)) {
                            $statusKontrakHtml = '<span class="badge badge-danger px-2 py-1" style="border-radius:12px">Habis</span>';
                        }
                        $garansiDuration = 3;
                        $garansiEnd = $startKontrak ? $startKontrak->copy()->addMonths($garansiDuration) : null;
                        $historyArts = collect();
                        if ($data->vacancy_id) {
                            $historyArts = \App\Models\Application::with('servant')
                                ->where('vacancy_id', $data->vacancy_id)
                                ->orderBy('work_start_date', 'asc')
                                ->get();
                        }
                    @endphp

                    <hr class="my-4">

                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-dark">
                            <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-file-contract mr-2"></i> Admin - Detail Kontrak</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-6 mb-4 mb-lg-0">
                                <div class="mb-4">
                                    <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Informasi Kontrak</h6>
                                    <p class="mb-1 text-dark">Durasi: {{ $durasiKontrak }} Bulan</p>
                                    <p class="mb-1 text-dark">Mulai: {{ $startKontrak ? $startKontrak->format('d M Y') : '-' }}</p>
                                    <p class="mb-1 text-dark">Selesai: {{ $endKontrak ? $endKontrak->format('d M Y') : '-' }}</p>
                                    <p class="mb-1 text-dark">Status: {!! $statusKontrakHtml !!}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Garansi</h6>
                                    <p class="mb-1 text-dark">Paket Garansi: {{ $data->garansi ? $data->garansi->name : '-' }}</p>
                                    <p class="mb-1 text-dark">Berakhir: {{ $garansiEnd ? $garansiEnd->format('d F Y') : '-' }}</p>
                                    <p class="mb-1 text-dark">Batas Tukar: {{ $data->garansi ? $data->garansi->max_replacements : 0 }}x</p>
                                    <p class="mb-3 text-dark">Sudah Digunakan: -</p>
                                    
                                    <div class="d-flex" style="gap: 10px; flex-wrap: wrap;">
                                        <button class="btn btn-primary btn-sm mb-1" style="border-radius:6px;" data-toggle="modal" data-target="#extendWarrantyModal-{{ $data->id }}"><i class="fas fa-sync-alt mr-1"></i> Perpanjang Garansi</button>
                                        <button class="btn btn-info btn-sm mb-1" style="border-radius:6px;" data-toggle="modal" data-target="#extendContractModal-{{ $data->id }}"><i class="fas fa-file-contract mr-1"></i> Perpanjang Kontrak</button>
                                        <button class="btn btn-success btn-sm mb-1" style="border-radius:6px;" data-toggle="modal" data-target="#swapModal-{{ $data->id }}"><i class="fas fa-exchange-alt mr-1"></i> Tukar Pembantu</button>
                                        <button class="btn btn-danger btn-sm mb-1" style="border-radius:6px;" data-toggle="modal" data-target="#endContractModal-{{ $data->id }}"><i class="fas fa-times mr-1"></i> Akhiri Kontrak</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 border-left">
                                <div class="mb-4">
                                    <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">History ART</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm text-dark mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="border-top-0">Ke</th>
                                                    <th class="border-top-0">Nama</th>
                                                    <th class="border-top-0">Mulai</th>
                                                    <th class="border-top-0">Selesai</th>
                                                    <th class="border-top-0">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($historyArts as $index => $history)
                                                    @php
                                                        $histEnd = $history->work_end_date ? \Carbon\Carbon::parse($history->work_end_date) : null;
                                                        $histBadge = '<span class="badge badge-success px-2 py-1" style="border-radius:12px">Aktif</span>';
                                                        if ($history->status == 'reject' || $history->status == 'laidoff') {
                                                            $histBadge = '<span class="badge badge-danger px-2 py-1" style="border-radius:12px">Diganti</span>';
                                                        } else if ($histEnd && now()->gt($histEnd)) {
                                                            $histBadge = '<span class="badge badge-secondary px-2 py-1" style="border-radius:12px">Selesai</span>';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $history->servant ? $history->servant->name : '-' }}</td>
                                                        <td>{{ $history->work_start_date ? \Carbon\Carbon::parse($history->work_start_date)->format('d M y') : '-' }}</td>
                                                        <td>{{ $histEnd ? $histEnd->format('d M y') : '-' }}</td>
                                                        <td>{!! $histBadge !!}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="5" class="text-center text-muted">Belum ada history ART</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div>
                                    <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Riwayat Perpanjangan Garansi</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm text-dark mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="border-top-0">Tanggal</th>
                                                    <th class="border-top-0 text-right">Tambahan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">Belum ada perpanjangan</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endhasrole

            {{-- Include Upload Contract Modal --}}
            @hasrole('majikan|admin|superadmin')
                @include('cms.servant.modal.upload-contract', ['data' => $data])
            @endhasrole
        </div> {{-- End tab-pane file-kontrak --}}

        {{-- TAB 1: GAJI & KEHADIRAN --}}
        <div class="tab-pane fade show active" id="gaji" role="tabpanel" aria-labelledby="gaji-tab">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-list mr-1"></i> 
                        @if($data->salary_type == 'fee' || $data->is_infal)
                            Informasi Pembayaran Fee
                        @else
                            Informasi Pembayaran Kontrak
                        @endif
                    </h5>
                </div>
                    @if($data->salary_type == 'fee' || $data->is_infal)
                        {{-- FEE: Tampilkan tabel bulan otomatis (mirip kontrak) --}}
                        @php
                            $feeStart = $data->work_start_date ? \Carbon\Carbon::parse($data->work_start_date) : null;
                            $feeEndLimit = ($data->is_infal && $data->work_end_date) ? \Carbon\Carbon::parse($data->work_end_date) : null;
                            $feeNow = \Carbon\Carbon::now();
                            $feeTarget = ($feeEndLimit && $feeNow->greaterThan($feeEndLimit)) ? $feeEndLimit : $feeNow;
                            $feeMonthsDiff = 0;
                            if ($feeStart && $feeStart->lessThanOrEqualTo($feeTarget)) {
                                $feeMonthsDiff = $feeStart->diffInMonths($feeTarget) + 1; // include current month
                            }

                            // Tarif dasar per satuan (per jam/hari/minggu/bulan)
                            $feeTarifSatuan = $data->salary;
                            
                            // Tentukan label frekuensi dan satuan
                            $feeFreqLabel = 'Bulanan';
                            $feeSatuanLabel = 'Bulan';
                            $feeNeedQuantity = false; // apakah perlu input jumlah
                            if ($data->is_infal && $data->infal_frequency) {
                                $feeFreqLabel = match($data->infal_frequency) {
                                    'hourly' => 'Per Jam',
                                    'daily' => 'Harian',
                                    'weekly' => 'Mingguan',
                                    'monthly' => 'Bulanan',
                                    default => 'Bulanan'
                                };
                                $feeSatuanLabel = match($data->infal_frequency) {
                                    'hourly' => 'Jam',
                                    'daily' => 'Hari',
                                    'weekly' => 'Minggu',
                                    'monthly' => 'Bulan',
                                    default => 'Bulan'
                                };
                                $feeNeedQuantity = in_array($data->infal_frequency, ['hourly', 'daily', 'weekly']);
                            }

                            // Helper function: hitung tagihan majikan dan gaji mitra dari subtotal gaji
                            // Ini dipakai per-row berdasarkan quantity
                            $calcFeeTotals = function($gajiPokok) use ($data) {
                                $totalTagihan = $gajiPokok;
                                $totalGajiMitra = $gajiPokok;
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
                                    $totalTagihan = $gajiPokok + $clientFees;

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
                                    $totalGajiMitra = $gajiPokok - $mitraDeductions;
                                }
                                return ['tagihan' => ceil($totalTagihan), 'mitra' => ceil($totalGajiMitra)];
                            };

                            // Hitung default (1 satuan) untuk keperluan display
                            $feeDefaultTotals = $calcFeeTotals($feeTarifSatuan);
                        @endphp

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> Mode Fee (<strong class="text-primary">{{ $feeFreqLabel }}</strong>):
                            @if($feeNeedQuantity)
                                Tarif <strong class="text-primary">Rp. {{ number_format($feeTarifSatuan, 0, ',', '.') }}</strong> / {{ $feeSatuanLabel }}.
                                Majikan mengisi jumlah {{ strtolower($feeSatuanLabel) }} kerja saat upload pembayaran, lalu total tagihan dihitung otomatis berdasarkan skema biaya.
                            @else
                                Majikan langsung upload bukti pembayaran setiap periode. 
                                Nominal per periode: <strong class="text-primary">Rp. {{ number_format($feeTarifSatuan, 0, ',', '.') }}</strong>.
                            @endif
                        </div>

                        @if($feeStart && $feeMonthsDiff > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" width="100%">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Bulan</th>
                                            @if($feeNeedQuantity)
                                                <th>Tarif / {{ $feeSatuanLabel }}</th>
                                                <th>Jumlah {{ $feeSatuanLabel }}</th>
                                            @endif
                                            <th>Nominal Gaji</th>
                                            @hasrole('superadmin|admin|owner|majikan')
                                                <th>Tagihan Majikan</th>
                                                <th>Bukti Pembayaran (Majikan)</th>
                                            @endhasrole
                                            @hasrole('superadmin|admin|owner|pembantu')
                                                <th>Gaji Diterima (Mitra)</th>
                                                <th>Bukti Dibayar (Mitra)</th>
                                            @endhasrole
                                            @hasrole('superadmin|admin|majikan')
                                                <th>Aksi</th>
                                            @endhasrole
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i = 0; $i < $feeMonthsDiff; $i++)
                                            @php
                                                $feeMonthDate = $feeStart->copy()->addMonths($i);
                                                $feeMonthStr = $feeMonthDate->format('Y-m');
                                                $feeSalaryRecord = collect($salaries)->first(function($s) use ($feeMonthStr) {
                                                    return \Carbon\Carbon::parse($s->month)->format('Y-m') === $feeMonthStr;
                                                });

                                                // Menghitung rentang tanggal untuk bulan/baris ini
                                                $monthStart = $feeMonthDate->copy()->startOfMonth();
                                                $monthEnd = $feeMonthDate->copy()->endOfMonth();
                                                if ($feeStart->greaterThan($monthStart)) {
                                                    $monthStart = $feeStart->copy();
                                                }
                                                if ($feeEndLimit && $feeEndLimit->lessThan($monthEnd)) {
                                                    $monthEnd = $feeEndLimit->copy();
                                                }

                                                // Hitung estimasi kuantitas untuk UI jika belum ada
                                                $estQuantity = 0;
                                                if ($feeNeedQuantity) {
                                                    if ($data->infal_frequency == 'daily') {
                                                        $estQuantity = $monthStart->diffInDays($monthEnd) + 1;
                                                    } elseif ($data->infal_frequency == 'hourly') {
                                                        $daysWorked = $monthStart->diffInDays($monthEnd) + 1;
                                                        if ($data->infal_time_in && $data->infal_time_out) {
                                                            $ti = \Carbon\Carbon::parse($data->infal_time_in);
                                                            $to = \Carbon\Carbon::parse($data->infal_time_out);
                                                            // Asumsi jika melewati tengah malam
                                                            if ($to->lessThan($ti)) {
                                                                $to->addDay();
                                                            }
                                                            $hoursPerDay = $ti->diffInHours($to);
                                                            $estQuantity = $daysWorked * $hoursPerDay;
                                                        } else {
                                                            $estQuantity = $daysWorked * 8; // Default 8 jam/hari
                                                        }
                                                    } elseif ($data->infal_frequency == 'weekly') {
                                                        $estQuantity = ceil(($monthStart->diffInDays($monthEnd) + 1) / 7);
                                                    }
                                                }

                                                // Hitung gaji dan tagihan per baris berdasarkan quantity nyata/estimasi
                                                $rowQuantity = $feeSalaryRecord && $feeSalaryRecord->quantity ? $feeSalaryRecord->quantity : null;
                                                $displayQuantity = $rowQuantity ?: $estQuantity;

                                                if ($feeNeedQuantity && $displayQuantity) {
                                                    $rowGajiPokok = $feeTarifSatuan * $displayQuantity;
                                                } else {
                                                    $rowGajiPokok = $feeTarifSatuan; // default 1 satuan / flat bulanan
                                                }
                                                $rowTotals = $calcFeeTotals($rowGajiPokok);
                                                $rowTagihanMajikan = $rowTotals['tagihan'];
                                                $rowGajiMitra = $rowTotals['mitra'];
                                            @endphp
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>
                                                    {{ $feeMonthDate->format('F Y') }}
                                                    @if($data->is_infal)
                                                        <br><small class="text-muted text-nowrap">({{ $monthStart->format('d M Y') }} - {{ $monthEnd->format('d M Y') }})</small>
                                                    @endif
                                                </td>
                                                @if($feeNeedQuantity)
                                                    <td>Rp. {{ number_format($feeTarifSatuan, 0, ',', '.') }}</td>
                                                    <td>
                                                        @if($displayQuantity)
                                                            <span class="badge {{ $rowQuantity ? 'badge-primary' : 'badge-secondary' }} px-2 py-1" style="white-space: nowrap;">
                                                                {{ $displayQuantity }} {{ $feeSatuanLabel }}
                                                                {!! !$rowQuantity ? '<br><small>(Estimasi)</small>' : '' !!}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                @endif
                                                <td>Rp. {{ number_format($rowGajiPokok, 0, ',', '.') }}</td>
                                                
                                                @hasrole('superadmin|admin|owner|majikan')
                                                    <td>Rp. {{ number_format($rowTagihanMajikan, 0, ',', '.') }}</td>
                                                    <td class="text-center">
                                                    @if ($feeSalaryRecord && $feeSalaryRecord->payment_majikan_image)
                                                        @php
                                                            $filePath = storage_path('app/public/payments/' . $feeSalaryRecord->payment_majikan_image);
                                                        @endphp
                                                        @if (file_exists($filePath))
                                                            @if (Str::endsWith($feeSalaryRecord->payment_majikan_image, ['.jpg', '.jpeg', '.png', '.gif']))
                                                                <img src="{{ route('getFile', ['path' => 'payments', 'fileName' => $feeSalaryRecord->payment_majikan_image]) }}" class="img-fluid zoomable-image" style="max-height: 80px; cursor: pointer;" data-toggle="modal" data-target="#verifyMajikanPaymentModal-{{ $i }}">
                                                            @elseif (Str::endsWith($feeSalaryRecord->payment_majikan_image, ['.pdf']))
                                                                <a href="#" data-toggle="modal" data-target="#verifyMajikanPaymentModal-{{ $i }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf mr-1"></i> Lihat PDF</a>
                                                            @else
                                                                <p>Format tidak didukung.</p>
                                                            @endif
                                                        @else
                                                            <p>File tidak ditemukan.</p>
                                                        @endif
                                                        <br>
                                                        @if($feeSalaryRecord->payment_majikan_status == 'waiting')
                                                            <span class="badge badge-warning mt-1"><i class="fas fa-clock mr-1"></i> Menunggu Verifikasi</span>
                                                        @elseif($feeSalaryRecord->payment_majikan_status == 'verified')
                                                            <span class="badge badge-success mt-1"><i class="fas fa-check-circle mr-1"></i> Terverifikasi</span>
                                                        @elseif($feeSalaryRecord->payment_majikan_status == 'rejected')
                                                            <span class="badge badge-danger mt-1"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                                                        @endif
                                                    @else
                                                        @if($feeSalaryRecord && $feeSalaryRecord->payment_majikan_status == 'rejected')
                                                            <span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Ditolak - Upload Ulang</span>
                                                        @else
                                                            Majikan Belum Membayar
                                                        @endif
                                                    @endif
                                                    </td>
                                                @endhasrole

                                                @hasrole('superadmin|admin|owner|pembantu')
                                                    <td>Rp. {{ number_format($rowGajiMitra, 0, ',', '.') }}</td>
                                                    <td class="text-center">
                                                    @if ($feeSalaryRecord && $feeSalaryRecord->payment_pembantu_image)
                                                        @php
                                                            $filePath = storage_path('app/public/payments/' . $feeSalaryRecord->payment_pembantu_image);
                                                        @endphp
                                                        @if (file_exists($filePath))
                                                            @if (Str::endsWith($feeSalaryRecord->payment_pembantu_image, ['.jpg', '.jpeg', '.png', '.gif']))
                                                                <img src="{{ route('getFile', ['path' => 'payments', 'fileName' => $feeSalaryRecord->payment_pembantu_image]) }}" class="img-fluid zoomable-image" style="max-height: 100px;">
                                                            @elseif (Str::endsWith($feeSalaryRecord->payment_pembantu_image, ['.pdf']))
                                                                <iframe src="{{ route('getFile', ['path' => 'payments', 'fileName' => $feeSalaryRecord->payment_pembantu_image]) }}" width="100%" height="100px"></iframe>
                                                            @else
                                                                <p>Format tidak didukung.</p>
                                                            @endif
                                                        @else
                                                            <p>File tidak ditemukan.</p>
                                                        @endif
                                                    @else
                                                        Belum Dibayarkan
                                                    @endif
                                                    </td>
                                                @endhasrole

                                                @hasrole('superadmin|admin|majikan')
                                                    <td class="text-center">
                                                    @hasrole('majikan')
                                                        @if (!$feeSalaryRecord || !$feeSalaryRecord->payment_majikan_image)
                                                            <a href="#" class="btn btn-sm btn-primary mb-1" data-toggle="modal" data-target="#paymentMajikanFeeModal-{{ $i }}"><i class="fas fa-upload mr-1"></i> Upload Pembayaran</a>
                                                            @include('cms.servant.modal.payment-majikan-fee', ['data' => $data, 'month' => $feeMonthStr, 'index' => $i, 'totalTagihan' => $rowTagihanMajikan, 'tarifSatuan' => $feeTarifSatuan, 'satuanLabel' => $feeSatuanLabel, 'needQuantity' => $feeNeedQuantity, 'defaultQuantity' => $displayQuantity])
                                                        @elseif($feeSalaryRecord && $feeSalaryRecord->payment_majikan_status == 'waiting')
                                                            <span class="badge badge-warning"><i class="fas fa-hourglass-half mr-1"></i> Menunggu Verifikasi</span>
                                                        @elseif($feeSalaryRecord && $feeSalaryRecord->payment_majikan_status == 'verified')
                                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Terverifikasi</span>
                                                        @elseif($feeSalaryRecord && $feeSalaryRecord->payment_majikan_status == 'rejected')
                                                            <a href="#" class="btn btn-sm btn-warning mb-1" data-toggle="modal" data-target="#paymentMajikanFeeModal-{{ $i }}"><i class="fas fa-redo mr-1"></i> Upload Ulang</a>
                                                            @include('cms.servant.modal.payment-majikan-fee', ['data' => $data, 'month' => $feeMonthStr, 'index' => $i, 'totalTagihan' => $rowTagihanMajikan, 'tarifSatuan' => $feeTarifSatuan, 'satuanLabel' => $feeSatuanLabel, 'needQuantity' => $feeNeedQuantity, 'defaultQuantity' => $displayQuantity])
                                                        @endif
                                                    @endhasrole
                                                    @hasrole('superadmin|admin')
                                                        @if (!$feeSalaryRecord || !$feeSalaryRecord->payment_majikan_image)
                                                            <button class="btn btn-sm btn-secondary mb-1" disabled><i class="fas fa-hourglass-half mr-1"></i> Menunggu Majikan</button>
                                                        @endif
                                                        @if ($feeSalaryRecord && $feeSalaryRecord->payment_majikan_status == 'waiting')
                                                            <a href="#" class="btn btn-sm btn-success mb-1" data-toggle="modal" data-target="#verifyMajikanPaymentModal-{{ $i }}"><i class="fas fa-check-circle mr-1"></i> Verifikasi</a>
                                                            @include('cms.servant.modal.verify-majikan-payment', ['data' => $data, 'month' => $feeMonthStr, 'index' => $i, 'salaryRecord' => $feeSalaryRecord])
                                                        @endif
                                                        <a href="#" class="btn btn-sm btn-info mb-1" data-toggle="modal" data-target="#paymentAdminFeeModal-{{ $i }}"><i class="fas fa-money-bill-wave mr-1"></i> Bayar Mitra</a>
                                                        @include('cms.servant.modal.payment-admin-fee', ['data' => $data, 'month' => $feeMonthStr, 'index' => $i, 'salaryRecord' => $feeSalaryRecord])
                                                    @endhasrole
                                                    </td>
                                                @endhasrole
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted"><i class="fas fa-calendar-times mb-2 fa-2x"></i><br>Tanggal mulai kerja belum diatur atau belum mencapai 1 bulan.</p>
                            </div>
                        @endif
                    @elseif($data->salary_type == 'contract')
                        @php
                            $start = $data->work_start_date ? \Carbon\Carbon::parse($data->work_start_date) : null;
                            $end = $data->work_end_date ? \Carbon\Carbon::parse($data->work_end_date) : null;
                            $monthsDiff = 0;
                            if ($start && $end && $start->lessThan($end)) {
                                $monthsDiff = $start->diffInMonths($end);
                            }
                        @endphp
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> Mode Kontrak: Berikut adalah daftar bulan yang perlu dibayarkan oleh majikan sesuai dengan durasi (<strong class="text-primary">{{ $monthsDiff }} Bulan</strong>).
                        </div>

                        @if($monthsDiff > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" width="100%">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Bulan</th>
                                            <th>Nominal Gaji</th>
                                            @hasrole('superadmin|admin|owner|majikan')
                                                <th>Bukti Pembayaran (Majikan)</th>
                                            @endhasrole
                                            @hasrole('superadmin|admin|owner|pembantu')
                                                <th>Bukti Dibayar (Mitra)</th>
                                            @endhasrole
                                            @hasrole('superadmin|admin|majikan')
                                                <th>Aksi</th>
                                            @endhasrole
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i = 0; $i < $monthsDiff; $i++)
                                            @php
                                                $monthDate = $start->copy()->addMonths($i);
                                                $monthStr = $monthDate->format('Y-m');
                                                $salaryRecord = collect($salaries)->first(function($s) use ($monthStr) {
                                                    return \Carbon\Carbon::parse($s->month)->format('Y-m') === $monthStr;
                                                });
                                            @endphp
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $monthDate->format('F Y') }}</td>
                                                <td>Rp. {{ number_format($data->salary, 0, ',', '.') }}</td>
                                                
                                                @hasrole('superadmin|admin|owner|majikan')
                                                    <td class="text-center">
                                                    @if ($salaryRecord && $salaryRecord->payment_majikan_image)
                                                        @php
                                                            $filePath = storage_path('app/public/payments/' . $salaryRecord->payment_majikan_image);
                                                        @endphp
                                                        @if (file_exists($filePath))
                                                            @if (Str::endsWith($salaryRecord->payment_majikan_image, ['.jpg', '.jpeg', '.png', '.gif']))
                                                                <img src="{{ route('getFile', ['path' => 'payments', 'fileName' => $salaryRecord->payment_majikan_image]) }}" class="img-fluid zoomable-image" style="max-height: 80px; cursor: pointer;" data-toggle="modal" data-target="#verifyMajikanPaymentModal-{{ $i }}">
                                                            @elseif (Str::endsWith($salaryRecord->payment_majikan_image, ['.pdf']))
                                                                <a href="#" data-toggle="modal" data-target="#verifyMajikanPaymentModal-{{ $i }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf mr-1"></i> Lihat PDF</a>
                                                            @else
                                                                <p>Format tidak didukung.</p>
                                                            @endif
                                                        @else
                                                            <p>File tidak ditemukan.</p>
                                                        @endif
                                                        <br>
                                                        @if($salaryRecord->payment_majikan_status == 'waiting')
                                                            <span class="badge badge-warning mt-1"><i class="fas fa-clock mr-1"></i> Menunggu Verifikasi</span>
                                                        @elseif($salaryRecord->payment_majikan_status == 'verified')
                                                            <span class="badge badge-success mt-1"><i class="fas fa-check-circle mr-1"></i> Terverifikasi</span>
                                                        @elseif($salaryRecord->payment_majikan_status == 'rejected')
                                                            <span class="badge badge-danger mt-1"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                                                        @endif
                                                    @else
                                                        @if($salaryRecord && $salaryRecord->payment_majikan_status == 'rejected')
                                                            <span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Ditolak - Upload Ulang</span>
                                                        @else
                                                            Majikan Belum Membayar
                                                        @endif
                                                    @endif
                                                    </td>
                                                @endhasrole

                                                @hasrole('superadmin|admin|owner|pembantu')
                                                    <td class="text-center">
                                                    @if ($salaryRecord && $salaryRecord->payment_pembantu_image)
                                                        @php
                                                            $filePath = storage_path('app/public/payments/' . $salaryRecord->payment_pembantu_image);
                                                        @endphp
                                                        @if (file_exists($filePath))
                                                            @if (Str::endsWith($salaryRecord->payment_pembantu_image, ['.jpg', '.jpeg', '.png', '.gif']))
                                                                <img src="{{ route('getFile', ['path' => 'payments', 'fileName' => $salaryRecord->payment_pembantu_image]) }}" class="img-fluid zoomable-image" style="max-height: 100px;">
                                                            @elseif (Str::endsWith($salaryRecord->payment_pembantu_image, ['.pdf']))
                                                                <iframe src="{{ route('getFile', ['path' => 'payments', 'fileName' => $salaryRecord->payment_pembantu_image]) }}" width="100%" height="100px"></iframe>
                                                            @else
                                                                <p>Format tidak didukung.</p>
                                                            @endif
                                                        @else
                                                            <p>File tidak ditemukan.</p>
                                                        @endif
                                                    @else
                                                        Belum Dibayarkan
                                                    @endif
                                                    </td>
                                                @endhasrole

                                                @hasrole('superadmin|admin|majikan')
                                                    <td class="text-center">
                                                    @hasrole('majikan')
                                                        @if (!$salaryRecord || !$salaryRecord->payment_majikan_image)
                                                            <a href="#" class="btn btn-sm btn-primary mb-1" data-toggle="modal" data-target="#paymentMajikanContractModal-{{ $i }}"><i class="fas fa-upload mr-1"></i> Upload Pembayaran</a>
                                                            @include('cms.servant.modal.payment-majikan-contract', ['data' => $data, 'month' => $monthStr, 'index' => $i])
                                                        @elseif($salaryRecord && $salaryRecord->payment_majikan_status == 'waiting')
                                                            <span class="badge badge-warning"><i class="fas fa-hourglass-half mr-1"></i> Menunggu Verifikasi</span>
                                                        @elseif($salaryRecord && $salaryRecord->payment_majikan_status == 'verified')
                                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Terverifikasi</span>
                                                        @elseif($salaryRecord && $salaryRecord->payment_majikan_status == 'rejected')
                                                            <a href="#" class="btn btn-sm btn-warning mb-1" data-toggle="modal" data-target="#paymentMajikanContractModal-{{ $i }}"><i class="fas fa-redo mr-1"></i> Upload Ulang</a>
                                                            @include('cms.servant.modal.payment-majikan-contract', ['data' => $data, 'month' => $monthStr, 'index' => $i])
                                                        @endif
                                                    @endhasrole
                                                    @hasrole('superadmin|admin')
                                                        @if (!$salaryRecord || !$salaryRecord->payment_majikan_image)
                                                            <button class="btn btn-sm btn-secondary mb-1" disabled><i class="fas fa-hourglass-half mr-1"></i> Menunggu Majikan</button>
                                                        @endif
                                                        @if ($salaryRecord && $salaryRecord->payment_majikan_status == 'waiting')
                                                            <a href="#" class="btn btn-sm btn-success mb-1" data-toggle="modal" data-target="#verifyMajikanPaymentModal-{{ $i }}"><i class="fas fa-check-circle mr-1"></i> Verifikasi</a>
                                                            @include('cms.servant.modal.verify-majikan-payment', ['data' => $data, 'month' => $monthStr, 'index' => $i, 'salaryRecord' => $salaryRecord])
                                                        @endif
                                                        <a href="#" class="btn btn-sm btn-info mb-1" data-toggle="modal" data-target="#paymentAdminContractModal-{{ $i }}"><i class="fas fa-money-bill-wave mr-1"></i> Bayar Mitra</a>
                                                        @include('cms.servant.modal.payment-admin-contract', ['data' => $data, 'month' => $monthStr, 'index' => $i, 'salaryRecord' => $salaryRecord])
                                                    @endhasrole
                                                    </td>
                                                @endhasrole
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted"><i class="fas fa-calendar-times mb-2 fa-2x"></i><br>Durasi kontrak kurang dari 1 bulan atau tanggal belum diatur dengan benar.</p>
                            </div>
                        @endif
                    @endif
            </div> {{-- End TAB 1 content layer --}}
        </div> {{-- End TAB 1 gaji --}}
            
        {{-- TAB 4: GARANSI (If applicable) --}}
        @if($data->salary_type == 'contract' && $data->garansi_id)
        <div class="tab-pane fade" id="garansi" role="tabpanel" aria-labelledby="garansi-tab">
            <div class="mb-3 d-flex justify-content-between align-items-center pb-2 border-bottom">
                <h5 class="m-0 font-weight-bold text-dark"><i class="fas fa-shield-alt mr-1"></i> Informasi Tagihan Garansi</h5>
            </div>
            <div>
                <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> Pembayaran tagihan garansi ini ditujukan ke pihak Sipembantu sesuai dengan kesepakatan paket <strong class="text-primary">{{ $data->garansi->name }}</strong>.
                    </div>
                    @if(isset($warrantyPayments) && $warrantyPayments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered text-center" width="100%">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Bulan</th>
                                    <th>Nominal Tagihan</th>
                                    <th>Bukti Pembayaran</th>
                                    <th>Status</th>
                                    @hasrole('majikan|superadmin|admin')
                                    <th>Aksi</th>
                                    @endhasrole
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($warrantyPayments as $index => $wp)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($wp->month_date)->format('F Y') }}</td>
                                    <td>Rp. {{ number_format($wp->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($wp->payment_image)
                                            @php
                                                $wpPath = storage_path('app/public/warranty_payments/' . $wp->payment_image);
                                            @endphp
                                            @if (file_exists($wpPath))
                                                @if (Str::endsWith($wp->payment_image, ['.jpg', '.jpeg', '.png', '.gif']))
                                                    <img src="{{ route('getFile', ['path' => 'warranty_payments', 'fileName' => basename($wp->payment_image)]) }}" class="img-fluid zoomable-image" style="max-height: 80px;">
                                                @else
                                                    <a href="{{ route('getFile', ['path' => 'warranty_payments', 'fileName' => basename($wp->payment_image)]) }}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-file"></i> Lihat Bukti</a>
                                                @endif
                                            @else
                                                <small class="text-danger">File tidak ditemukan</small>
                                            @endif
                                        @else
                                            <small class="text-muted">Belum Diupload</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($wp->status == 'pending')
                                            <span class="badge badge-warning py-1 px-2">Belum Bayar</span>
                                        @elseif($wp->status == 'waiting')
                                            <span class="badge badge-info py-1 px-2">Menunggu Verifikasi</span>
                                        @elseif($wp->status == 'paid')
                                            <span class="badge badge-success py-1 px-2">Lunas</span>
                                        @endif
                                    </td>
                                    @hasrole('majikan|superadmin|admin')
                                    <td>
                                        @hasrole('majikan')
                                            @if($wp->status != 'paid')
                                                <a href="#" class="btn btn-sm btn-primary mb-1" data-toggle="modal" data-target="#uploadWarrantyPaymentModal-{{ $wp->id }}"><i class="fas fa-upload mr-1"></i> Upload Bukti</a>
                                                @include('cms.servant.modal.upload-warranty', ['wp' => $wp, 'appId' => $data->id])
                                            @endif
                                        @endhasrole
                                        @hasrole('superadmin|admin')
                                            @if(in_array($wp->status, ['pending', 'waiting']))
                                                <a href="#" class="btn btn-sm btn-info mb-1" data-toggle="modal" data-target="#verifyWarrantyPaymentModal-{{ $wp->id }}"><i class="fas fa-check-circle mr-1"></i> Verifikasi</a>
                                                @include('cms.servant.modal.verify-warranty', ['wp' => $wp, 'appId' => $data->id])
                                            @endif
                                        @endhasrole
                                    </td>
                                    @endhasrole
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-muted">Data tagihan belum tersedia.</p>
                    </div>
                    @endif
            </div>
        </div> {{-- End TAB 4 garansi --}}
        @endif

        {{-- TAB 3: DETAIL PEKERJAAN (Role Specific) --}}
        <div class="tab-pane fade" id="status" role="tabpanel" aria-labelledby="status-tab">

    {{-- INFO CARD: Tipe Pekerjaan & Multi-Employer --}}
    @php
        $infoFreqLabel = 'Bulanan';
        $infoFreqIcon = 'fa-calendar-alt';
        $infoFreqColor = 'primary';
        if ($data->is_infal && $data->infal_frequency) {
            $infoFreqLabel = match($data->infal_frequency) {
                'hourly' => 'Per Jam',
                'daily' => 'Harian',
                'weekly' => 'Mingguan',
                'monthly' => 'Bulanan',
                default => 'Bulanan'
            };
            $infoFreqIcon = match($data->infal_frequency) {
                'hourly' => 'fa-hourglass-half',
                'daily' => 'fa-sun',
                'weekly' => 'fa-calendar-week',
                'monthly' => 'fa-calendar-alt',
                default => 'fa-calendar-alt'
            };
            $infoFreqColor = 'info';
        }

        // Multi-Employer: cek apakah pekerja ini punya pekerjaan aktif lainnya
        $otherActiveJobs = collect();
        if ($data->servant_id && ($data->salary_type == 'fee' || $data->is_infal)) {
            $otherActiveJobs = \App\Models\Application::with(['vacancy.user', 'employe'])
                ->where('servant_id', $data->servant_id)
                ->where('id', '!=', $data->id)
                ->where('status', 'accepted')
                ->get();
        }
        $isMultiEmployer = $otherActiveJobs->count() > 0;
    @endphp

    <div class="row mb-4">
        <div class="col-12">
            <div class="border rounded p-3" style="background: linear-gradient(135deg, #f8f9fc 0%, #eef2f7 100%);">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-briefcase mr-2"></i> Informasi Pekerjaan</h5>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block mb-1"><i class="fas fa-{{ $infoFreqIcon }} mr-1"></i> Tipe & Frekuensi</small>
                        <span class="badge badge-{{ $infoFreqColor }} px-3 py-2" style="border-radius: 20px; font-size: 0.9rem;">
                            @if($data->salary_type == 'contract')
                                <i class="fas fa-file-contract mr-1"></i> Kontrak
                            @elseif($data->is_infal)
                                <i class="fas fa-{{ $infoFreqIcon }} mr-1"></i> Fee Infal - {{ $infoFreqLabel }}
                            @else
                                <i class="fas fa-hand-holding-usd mr-1"></i> Fee Reguler
                            @endif
                        </span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block mb-1"><i class="fas fa-money-check-alt mr-1"></i> Gaji per Periode</small>
                        <span class="font-weight-bold text-success" style="font-size: 1.1rem;">
                            Rp {{ number_format($data->salary, 0, ',', '.') }}
                            <small class="text-muted">
                                / {{ $data->salary_type == 'contract' ? 'Bulan' : ($data->is_infal ? $infoFreqLabel : 'Bulan') }}
                            </small>
                        </span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block mb-1"><i class="fas fa-users mr-1"></i> Status Multi-Employer</small>
                        @if($isMultiEmployer)
                            <span class="badge badge-success px-3 py-2" style="border-radius: 20px; font-size: 0.85rem;">
                                <i class="fas fa-check-circle mr-1"></i> Aktif di {{ $otherActiveJobs->count() + 1 }} Tempat Kerja
                            </span>
                        @elseif($data->salary_type == 'contract')
                            <span class="badge badge-secondary px-3 py-2" style="border-radius: 20px; font-size: 0.85rem;">
                                <i class="fas fa-lock mr-1"></i> Kontrak Eksklusif
                            </span>
                        @else
                            <span class="badge badge-light border px-3 py-2" style="border-radius: 20px; font-size: 0.85rem;">
                                <i class="fas fa-minus-circle mr-1"></i> 1 Tempat Kerja
                            </span>
                        @endif
                    </div>
                </div>

                @if($data->is_infal && $data->infal_frequency == 'hourly')
                <div class="row mt-2 pt-2 border-top">
                    <div class="col-md-4">
                        <small class="text-muted d-block mb-1"><i class="fas fa-sign-in-alt mr-1"></i> Jam Masuk</small>
                        <span class="font-weight-bold text-dark">{{ $data->infal_time_in ?? '-' }}</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block mb-1"><i class="fas fa-sign-out-alt mr-1"></i> Jam Keluar</small>
                        <span class="font-weight-bold text-dark">{{ $data->infal_time_out ?? '-' }}</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block mb-1"><i class="fas fa-coins mr-1"></i> Rate Per Jam</small>
                        <span class="font-weight-bold text-dark">Rp {{ number_format($data->infal_hourly_rate ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endif

                @if($isMultiEmployer)
                <div class="mt-3 pt-2 border-top">
                    <small class="text-muted d-block mb-2"><i class="fas fa-building mr-1"></i> <strong>Daftar Tempat Kerja Aktif Lainnya:</strong></small>
                    <div class="d-flex flex-wrap" style="gap: 8px;">
                        @foreach($otherActiveJobs as $otherJob)
                            @php
                                $otherEmployerName = '-';
                                if ($otherJob->vacancy && $otherJob->vacancy->user) {
                                    $otherEmployerName = $otherJob->vacancy->user->name;
                                } elseif ($otherJob->employe) {
                                    $otherEmployerName = $otherJob->employe->name;
                                }
                                $otherFreq = '';
                                if ($otherJob->is_infal && $otherJob->infal_frequency) {
                                    $otherFreq = ' (' . match($otherJob->infal_frequency) {
                                        'hourly' => 'Per Jam',
                                        'daily' => 'Harian',
                                        'weekly' => 'Mingguan',
                                        'monthly' => 'Bulanan',
                                        default => ''
                                    } . ')';
                                }
                            @endphp
                            <span class="badge badge-outline-primary border px-2 py-1" style="border-radius: 12px; font-size: 0.8rem;">
                                <i class="fas fa-user-tie mr-1"></i> {{ $otherEmployerName }} - Rp {{ number_format($otherJob->salary, 0, ',', '.') }}{{ $otherFreq }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SUPERADMIN DETAIL KONTRAK SECTION - Dipindah ke Tab File Kontrak --}}
    
    {{-- MAJIKAN KONTRAK SAYA SECTION --}}
    @hasrole('majikan')
        @php
            // Calculate Contract Information for Employer
            $startKontrak = $data->work_start_date ? \Carbon\Carbon::parse($data->work_start_date) : null;
            $endKontrak = $data->work_end_date ? \Carbon\Carbon::parse($data->work_end_date) : ($startKontrak ? $startKontrak->copy()->addMonths(12) : null);
            $durasiKontrak = $startKontrak && $endKontrak ? round($startKontrak->floatDiffInMonths($endKontrak)) : 12;
            
            $sisaKontrak = 0;
            if ($endKontrak && now()->lessThan($endKontrak)) {
                $sisaKontrak = round(now()->floatDiffInMonths($endKontrak));
            }
            
            $statusKontrakHtml = '<span class="badge badge-success px-2 py-1" style="border-radius:12px">Aktif</span>';
            if ($endKontrak && now()->gt($endKontrak)) {
                $statusKontrakHtml = '<span class="badge badge-danger px-2 py-1" style="border-radius:12px">Habis</span>';
            }

            // Garansi (Only if salary type is contract)
            $isContract = $data->salary_type == 'contract';
            $garansiDuration = 3; // Mock default 3 months
            $garansiEnd = $startKontrak ? $startKontrak->copy()->addMonths($garansiDuration) : null;
            $sisaGaransi = 0;
            if ($garansiEnd && now()->lessThan($garansiEnd)) {
                $sisaGaransi = round(now()->floatDiffInMonths($garansiEnd));
            }
            $sisaPergantian = 3; // Mock defaults
            
            // History ART Query removed per user request (moved to worker list page)
        @endphp

        <div class="row mb-4">
            <div class="col-12">
                <div class="border rounded p-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-dark">
                        <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-file-contract mr-2"></i> Kontrak Saya</h5>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12 mb-4 mb-lg-0">
                            {{-- Status Kontrak --}}
                            <div class="mb-4">
                                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Status Kontrak</h6>
                                <p class="mb-1 text-dark">Durasi: {{ $durasiKontrak }} Bulan</p>
                                <p class="mb-1 text-dark">Sisa Kontrak: {{ $sisaKontrak }} Bulan</p>
                                <p class="mb-1 text-dark">Status: {!! $statusKontrakHtml !!}</p>
                                
                                @if($isContract)
                                    <div class="mt-4">
                                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Garansi</h6>
                                        <p class="mb-1 text-dark">Sisa Garansi: {{ $sisaGaransi }} Bulan</p>
                                        <p class="mb-3 text-dark">Sisa Pergantian: {{ $data->garansi ? $data->garansi->max_replacements : 0 }}x</p>
                                        
                                        <div class="d-flex" style="gap: 10px;">
                                            <button class="btn btn-primary btn-sm" style="border-radius:6px;" data-toggle="modal" data-target="#extendWarrantyModal-{{ $data->id }}"><i class="fas fa-sync-alt mr-1"></i> Perpanjang Garansi</button>
                                            <button class="btn btn-success btn-sm" style="border-radius:6px;" data-toggle="modal" data-target="#swapModal-{{ $data->id }}"><i class="fas fa-exchange-alt mr-1"></i> Tukar Pembantu</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endhasrole
    {{-- PEMBANTU STATUS KERJA SAYA SECTION --}}
    @hasrole('pembantu')
        @php
            $startKontrak = $data->work_start_date ? \Carbon\Carbon::parse($data->work_start_date) : null;
            $endKontrak = $data->work_end_date ? \Carbon\Carbon::parse($data->work_end_date) : ($startKontrak ? $startKontrak->copy()->addMonths(12) : null);
            
            // Menggunakan ceil atau round untuk memastikan pembulatan jika ada koma (contohnya 2.166... menjadi 2 atau 3). 
            // Kita gunakan round() saja.
            $durasiKontrak = $startKontrak && $endKontrak ? round($startKontrak->floatDiffInMonths($endKontrak)) : 12;

            $sisaMasaKerja = 0;
            if ($startKontrak && $endKontrak && now()->lessThan($endKontrak)) {
                $sisaMasaKerja = round(now()->floatDiffInMonths($endKontrak));
            }
            $bulanBerjalan = max(0, $durasiKontrak - $sisaMasaKerja);
            $progressPersen = $durasiKontrak > 0 ? min(100, max(0, ($bulanBerjalan / $durasiKontrak) * 100)) : 0;

             $statusKontrakHtml = '<span class="badge badge-success px-2 py-1" style="border-radius:12px">Aktif</span>';
            if ($endKontrak && now()->gt($endKontrak)) {
                $statusKontrakHtml = '<span class="badge badge-danger px-2 py-1" style="border-radius:12px">Habis</span>';
            }

            $employerName = $data->vacancy_id != null && $data->vacancy && $data->vacancy->user ? $data->vacancy->user->name : ($data->employe ? $data->employe->name : '-');

            $gajiPokok = $data->salary;
            $totalNilaiKontrak = $gajiPokok * $durasiKontrak;

            $totalDibayar = 0;
            $statusGajiBulanIni = '<span class="badge badge-warning px-2 py-1" style="border-radius:12px">Belum Bayar</span>';
            
            foreach($salaries as $sal) {
                if ($sal->payment_pembantu_image) {
                    $totalDibayar += $sal->total_salary_pembantu;
                }
                
                // Cek status bulan ini
                if (\Carbon\Carbon::parse($sal->month)->format('Y-m') == \Carbon\Carbon::now()->format('Y-m')) {
                    if ($sal->payment_pembantu_image) {
                        $statusGajiBulanIni = '<span class="badge badge-success px-2 py-1" style="border-radius:12px">Lunas</span>';
                    } else if ($sal->payment_majikan_image) {
                        $statusGajiBulanIni = '<span class="badge badge-info px-2 py-1" style="border-radius:12px">Diproses Admin</span>';
                    }
                }
            }
            
            $riwayatPenempatan = \App\Models\Application::with(['vacancy.user', 'employe'])
                ->where('servant_id', $data->servant_id)
                ->whereIn('status', ['accepted', 'review', 'passed', 'verify', 'contract', 'choose'])
                ->orderBy('work_start_date', 'asc')
                ->get();
        @endphp

        <style>
            .pembantu-card { border-radius: 8px; border: none; }
            .pembantu-section { font-weight: bold; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px; color: #333; }
            .pembantu-info { margin-bottom: 8px; color: #555; }
            .pembantu-progress { background: #eee; height: 10px; border-radius: 20px; overflow: hidden; margin-top: 5px; }
            .pembantu-progress-bar { height: 100%; background: #2f80ed; }
            .pembantu-summary-box { background: #f9fafc; padding: 10px; border-radius: 6px; margin-top: 10px; border: 1px solid #e3e6f0; }
            .pembantu-table th { background: #f8f9fc; color: #4e73df; font-weight: bold; border-bottom: 2px solid #e3e6f0; }
            /* border-left-lg utility for larger screens */
            @media (min-width: 992px) {
                .border-left-lg { border-left: 1px solid #eee; }
            }
        </style>

        <div class="row mb-4">
            <div class="col-12">
                <div class="border rounded p-3 bg-white pembantu-card shadow-sm">
                    <h5 class="font-weight-bold text-dark mb-4 border-bottom border-dark pb-2"><i class="fas fa-file-contract mr-2"></i> Status Kerja Saya (Kontrak)</h5>
                    
                    <div class="row">
                        <!-- LEFT SIDE -->
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <div class="pembantu-section">Informasi Kontrak</div>
                            <div class="pembantu-info">Majikan: <strong>{{ $employerName }}</strong></div>
                            <div class="pembantu-info">Durasi Kontrak: <strong>{{ $durasiKontrak }} Bulan</strong></div>
                            <div class="pembantu-info">Mulai: <strong>{{ $startKontrak ? $startKontrak->format('d F Y') : '-' }}</strong></div>
                            <div class="pembantu-info">Selesai: <strong>{{ $endKontrak ? $endKontrak->format('d F Y') : '-' }}</strong></div>

                            <div class="pembantu-info mt-3">
                                Sisa Masa Kerja: <strong>{{ $sisaMasaKerja }} Bulan</strong>
                                <div class="pembantu-progress">
                                    <div class="pembantu-progress-bar" style="width: {{ $progressPersen }}%;"></div>
                                </div>
                                <small class="text-muted">{{ $bulanBerjalan }} dari {{ $durasiKontrak }} Bulan (Estimasi)</small>
                            </div>

                            <div class="pembantu-info mt-3">
                                Status Kontrak: {!! $statusKontrakHtml !!}
                            </div>
                        </div>

                        <!-- RIGHT SIDE -->
                        <div class="col-lg-6 border-left-lg pl-lg-4">
                            <div class="pembantu-section">Informasi Gaji</div>
                            <div class="pembantu-info">Gaji Pokok: <strong>Rp {{ number_format($gajiPokok, 0, ',', '.') }} / Bulan</strong></div>
                            <div class="pembantu-info">Total Nilai Kontrak: <strong>Rp {{ number_format($totalNilaiKontrak, 0, ',', '.') }}</strong></div>

                            <div class="pembantu-info mt-3">
                                Status Gaji Bulan Ini: {!! $statusGajiBulanIni !!}
                            </div>

                            <div class="pembantu-summary-box">
                                Total Gaji Sudah Diterima:<br>
                                <b class="text-success" style="font-size: 1.2rem;">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</b>
                            </div>
                        </div>
                    </div>

                    <!-- HISTORY GAJI -->
                    <div class="row mt-4 pt-3 border-top">
                        <div class="col-12">
                            <div class="pembantu-section">History Pembayaran Gaji</div>
                            <div class="table-responsive">
                                <table class="table table-bordered pembantu-table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Nominal Pembantu</th>
                                            <th>Status</th>
                                            <th>Tanggal Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salaries as $sal)
                                            @php
                                                $stat = '<span class="badge badge-warning px-2 py-1" style="border-radius:12px">Belum Bayar</span>';
                                                $tanggalBayar = '-';

                                                if ($sal->payment_pembantu_image) {
                                                    $stat = '<span class="badge badge-success px-2 py-1" style="border-radius:12px">Lunas</span>';
                                                    $tanggalBayar = $sal->updated_at ? $sal->updated_at->format('d-m-Y') : '-';
                                                } else if ($sal->payment_majikan_image) {
                                                    $stat = '<span class="badge badge-info px-2 py-1" style="border-radius:12px">Diproses Admin</span>';
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($sal->month)->format('F Y') }}</td>
                                                <td>Rp {{ number_format($sal->total_salary_pembantu, 0, ',', '.') }}</td>
                                                <td>{!! $stat !!}</td>
                                                <td>{{ $tanggalBayar }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Belum ada history pembayaran gaji</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- RIWAYAT PENEMPATAN -->
                    <div class="row mt-4 pt-3 border-top">
                        <div class="col-12">
                            <div class="pembantu-section">Riwayat Penempatan</div>
                            <div class="table-responsive">
                                <table class="table table-bordered pembantu-table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Majikan</th>
                                            <th>Mulai</th>
                                            <th>Selesai</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($riwayatPenempatan as $riwayat)
                                            @php
                                                $rStart = $riwayat->work_start_date ? \Carbon\Carbon::parse($riwayat->work_start_date) : null;
                                                $rEnd = $riwayat->work_end_date ? \Carbon\Carbon::parse($riwayat->work_end_date) : null;
                                                
                                                $rEmployer = $riwayat->vacancy_id != null && $riwayat->vacancy && $riwayat->vacancy->user ? $riwayat->vacancy->user->name : ($riwayat->employe ? $riwayat->employe->name : '-');
                                                
                                                $rBadge = '<span class="badge badge-success px-2 py-1" style="border-radius:12px">Aktif</span>';
                                                if ($riwayat->status == 'reject' || $riwayat->status == 'laidoff') {
                                                    $rBadge = '<span class="badge badge-danger px-2 py-1" style="border-radius:12px">Diganti/Keluar</span>';
                                                } else if ($rEnd && now()->gt($rEnd)) {
                                                    $rBadge = '<span class="badge badge-secondary px-2 py-1" style="border-radius:12px">Selesai</span>';
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $rEmployer }}</td>
                                                <td>{{ $rStart ? $rStart->format('d M Y') : '-' }}</td>
                                                <td>{{ $rEnd ? $rEnd->format('d M Y') : '-' }}</td>
                                                <td>{!! $rBadge !!}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Belum ada riwayat penempatan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endhasrole

        </div> {{-- End TAB 3 status-tab --}}
        
                    </div> {{-- End tab-content --}}
                </div> {{-- End card-body tabs --}}
            </div> {{-- End card --}}
        </div> {{-- End col-12 --}}
    </div> {{-- End row --}}

    {{-- ULASAN & RATING SECTION --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow p-4 pembantu-card">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-star text-warning mr-2"></i> Ulasan & Rating</h5>
                    @if($data->status === 'laidoff' && auth()->user()->hasAnyRole(['majikan', 'pembantu']))
                        @php
                            $hasReviewed = $data->reviews->where('reviewer_id', auth()->id())->isNotEmpty();
                        @endphp
                        @if(!$hasReviewed)
                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#reviewModal"><i class="fas fa-pen mr-1"></i> Berikan Ulasan</button>
                        @endif
                    @endif
                </div>

                <div class="row">
                    <div class="col-12">
                        @if($data->reviews->isEmpty())
                            <p class="text-muted text-center my-3">Belum ada ulasan untuk kontrak ini.</p>
                        @else
                            <div class="list-group">
                                @foreach($data->reviews as $review)
                                    <div class="list-group-item flex-column align-items-start border-left-warning mb-2 shadow-sm rounded">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1 font-weight-bold">
                                                {{ $review->reviewer->name }} 
                                                <small class="text-muted">({{ $review->reviewer->roles->first()->name }})</small>
                                                <i class="fas fa-arrow-right mx-1 text-muted" style="font-size: 0.8rem;"></i>
                                                {{ $review->reviewee->name }}
                                            </h6>
                                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-body' }}"></i>
                                            @endfor
                                        </div>
                                        <p class="mb-1 text-dark">"{{ $review->comment }}"</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @if($data->status === 'laidoff' && auth()->user()->hasAnyRole(['majikan', 'pembantu']))
        <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="reviewModalLabel"><i class="fas fa-star text-warning mr-1"></i> Berikan Ulasan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('worker.store-review', $data->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group text-center">
                                <label class="font-weight-bold text-dark">Rating (1-5)</label>
                                <div class="rating-stars mb-3" style="font-size: 2rem; display: flex; justify-content: center; gap: 10px; cursor: pointer;">
                                    <input type="radio" name="rating" id="star1" value="1" class="d-none" required> <label for="star1" class="mb-0"><i class="far fa-star text-warning" onclick="setRating(1)"></i></label>
                                    <input type="radio" name="rating" id="star2" value="2" class="d-none"> <label for="star2" class="mb-0"><i class="far fa-star text-warning" onclick="setRating(2)"></i></label>
                                    <input type="radio" name="rating" id="star3" value="3" class="d-none"> <label for="star3" class="mb-0"><i class="far fa-star text-warning" onclick="setRating(3)"></i></label>
                                    <input type="radio" name="rating" id="star4" value="4" class="d-none"> <label for="star4" class="mb-0"><i class="far fa-star text-warning" onclick="setRating(4)"></i></label>
                                    <input type="radio" name="rating" id="star5" value="5" class="d-none"> <label for="star5" class="mb-0"><i class="far fa-star text-warning" onclick="setRating(5)"></i></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="comment" class="font-weight-bold text-dark">Komentar <span class="text-danger">*</span></label>
                                <textarea name="comment" id="comment" class="form-control" rows="4" required maxlength="1000" placeholder="Tuliskan pengalaman Anda bekerja sama..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning text-dark font-weight-bold"><i class="fas fa-paper-plane mr-1"></i> Kirim Ulasan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('custom-script')
        <script>
            function setRating(rating) {
                const stars = document.querySelectorAll('.rating-stars i');
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('far');
                        star.classList.add('fas');
                    } else {
                        star.classList.remove('fas');
                        star.classList.add('far');
                    }
                });
            }
        </script>
        @endpush
    @endif
    @include('cms.servant.modal.contract-actions')
@endsection
