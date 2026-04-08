@extends('cms.layouts.main')

@section('content')
    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-balance-scale"></i> Rekonsiliasi Keuangan
        </h1>
    </div>

    {{-- BAGIAN 1: Filter + Tombol Export --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('recon.index') }}" class="row align-items-end">
                <div class="col-md-3 mb-2">
                    <label for="month" class="font-weight-bold">Bulan</label>
                    <select name="month" id="month" class="form-control">
                        @foreach ($monthNames as $num => $name)
                            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="year" class="font-weight-bold">Tahun</label>
                    <input type="number" name="year" id="year" class="form-control"
                        value="{{ $year }}" min="2020" max="2099">
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                <div class="col-md-5 mb-2 text-right">
                    <a href="{{ route('recon.export-excel', ['month' => $month, 'year' => $year]) }}"
                        class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('recon.export-pdf', ['month' => $month, 'year' => $year]) }}"
                        class="btn btn-danger ml-1">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- BAGIAN 2: Summary Cards --}}
    <div class="row mb-4">
        {{-- Total Masuk dari Majikan --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Masuk dari Majikan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalMasukMajikan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Keluar ke ART --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Keluar ke ART
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalKeluarArt, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fee Platform --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Fee Platform
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalFeePlatform, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Selisih --}}
        <div class="col-xl-3 col-md-6 mb-4">
            @if ($selisih == 0)
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Selisih — Balance ✓
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-success">
                                    Rp {{ number_format($selisih, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Selisih — Tidak Balance ✗
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-danger">
                                    Rp {{ number_format($selisih, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- BAGIAN 3: Tabel Penerimaan dari Majikan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                💳 Penerimaan dari Majikan — {{ $monthNames[$month] }} {{ $year }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama ART</th>
                            <th>Nama Majikan</th>
                            <th>Bulan</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>No. Referensi</th>
                            <th>Tgl. Verified</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salaries as $i => $salary)
                            @php
                                $app = $salary->application;
                                $namaMajikan = $app->employe->name ?? ($app->vacancy->user->name ?? '-');
                                $namaArt = $app->servant->name ?? '-';
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $namaArt }}</td>
                                <td>{{ $namaMajikan }}</td>
                                <td>{{ $monthNames[$salary->month->month] ?? '-' }}</td>
                                <td>Rp {{ number_format($salary->payment_majikan_amount ?? 0, 0, ',', '.') }}</td>
                                <td>{{ $salary->payment_majikan_method ?? '-' }}</td>
                                <td>{{ $salary->payment_majikan_ref_number ?? '-' }}</td>
                                <td>
                                    {{ $salary->payment_majikan_verified_at ? $salary->payment_majikan_verified_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    @if ($salary->payment_majikan_status === 'verified')
                                        <span class="badge badge-success">Verified ✓</span>
                                    @elseif ($salary->payment_majikan_status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-secondary">Belum Upload</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Tidak ada data untuk bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- BAGIAN 4: Tabel Pengeluaran ke ART --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                💸 Pengeluaran ke ART — {{ $monthNames[$month] }} {{ $year }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama ART</th>
                            <th>Bank</th>
                            <th>No. Rekening</th>
                            <th>Gaji Disepakati</th>
                            <th>Fee Platform</th>
                            <th>Gaji Bersih</th>
                            <th>No. Referensi</th>
                            <th>Tgl. Transfer</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salaries as $i => $salary)
                            @php
                                $app = $salary->application;
                                $servant = $app->servant;
                                $details = $servant->servantDetails ?? null;
                                $feePlatform = $salary->total_salary_majikan - $salary->total_salary_pembantu;
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $servant->name ?? '-' }}</td>
                                <td>{{ $details->bank_name ?? '-' }}</td>
                                <td>{{ $details->account_number ?? '-' }}</td>
                                <td>Rp {{ number_format($salary->total_salary, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($feePlatform, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($salary->total_salary_pembantu, 0, ',', '.') }}</td>
                                <td>{{ $salary->payment_pembantu_ref_number ?? '-' }}</td>
                                <td>
                                    {{ $salary->payment_pembantu_transfer_at ? $salary->payment_pembantu_transfer_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    @if ($salary->payment_pembantu_status === 'sudah')
                                        <span class="badge badge-success">Sudah ✓</span>
                                    @else
                                        <span class="badge badge-danger">Belum</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    Tidak ada data untuk bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
