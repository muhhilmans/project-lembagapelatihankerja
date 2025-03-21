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
        <div class="col-lg-4 mb-3 mb-lg-0">
            <div class="card shadow p-2">
                <div class="card-body">
                    @if ($data->servant->servantDetails->photo)
                        <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $data->servant->servantDetails->photo]) }}"
                            class="img-fluid rounded mx-auto d-block zoomable-image" style="max-height: 150px;"
                            alt="...">
                    @else
                        <img src="{{ asset('assets/img/undraw_rocket.svg') }}"
                            class="img-fluid rounded mx-auto d-block zoomable-image" style="max-height: 150px;"
                            alt="...">
                    @endif

                    <ul class="list-unstyled">
                        <li><i class="fas fa-user mr-2 mb-2"></i> {{ $data->servant->name }}</li>
                        <li><i class="fas fa-user-tie mr-2 mb-2"></i>
                            {{ $data->vacancy_id != null ? $data->vacancy->user->name : $data->employe->name }}
                        </li>
                        <li><i class="fas fa-clock mr-2 mb-2"></i>
                            {{ \Carbon\Carbon::parse($data->work_start_date)->format('d F Y') }}</li>
                        <li><i class="fas fa-money-bill-wave mr-1 mb-2"></i>
                            @hasrole('majikan')
                                @php
                                    $salary = $data->salary;
                                    $service = $salary * $data->schemaSalary->adds_client;
                                    $gaji = $salary + $service;
                                @endphp

                                Rp. {{ number_format($gaji, 0, ',', '.') }}
                            @endhasrole

                            @hasrole('superadmin|admin|owner|pembantu')
                                @php
                                    $salary = $data->salary;
                                    $service = $salary * $data->schemaSalary->adds_mitra;
                                    $gaji = $salary - $service;
                                @endphp

                                Rp. {{ number_format($gaji, 0, ',', '.') }}
                            @endhasrole
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h5 font-weight-bold">Detail Kehadiran</h1>
                        @if (!$salaries->contains(fn($data) => \Carbon\Carbon::parse($data->month)->format('Y-m') == \Carbon\Carbon::now()->format('Y-m')))
                            <a href="#" class="btn btn-sm btn-primary" data-toggle="modal"
                                data-target="#createKehadiranModal-{{ $data->id }}"><i class="fas fa-plus mr-1"></i>
                                Kehadiran</a>
                            @include('cms.servant.modal.create-presence', ['data' => $data])
                        @else
                            <a href="#" class="btn btn-sm btn-secondary disabled">Sudah Mengisi Kehadiran</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Bulan</th>
                                    <th>Hadir</th>
                                    @hasrole('superadmin|admin|owner|majikan')
                                        <th>Gaji (Dengan Tambahan)</th>
                                        <th>Bukti Pembayaran</th>
                                    @endhasrole
                                    @hasrole('superadmin|admin|owner|pembantu')
                                        <th>Gaji (Dengan Potongan)</th>
                                        <th>Bukti Dibayar</th>
                                    @endhasrole
                                    @hasrole('superadmin|admin|majikan')
                                        <th>Aksi</th>
                                    @endhasrole
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salaries as $salary)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($salary->month)->format('F Y') }}</td>
                                        <td class="text-center">
                                            {{ $salary->presence }} Hari
                                        </td>
                                        @hasrole('superadmin|admin|owner|majikan')
                                            <td class="text-center">
                                                Rp. {{ number_format($salary->total_salary_majikan, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($salary->payment_majikan_image)
                                                    @php
                                                        $filePath = storage_path(
                                                            'app/public/payments/' . $salary->payment_majikan_image,
                                                        );
                                                    @endphp

                                                    @if (file_exists($filePath))
                                                        @if (Str::endsWith($salary->payment_majikan_image, ['.jpg', '.jpeg', '.png', '.gif']))
                                                            <img src="{{ route('getFile', ['path' => 'payments', 'fileName' => $salary->payment_majikan_image]) }}"
                                                                alt="Preview" class="img-fluid zoomable-image"
                                                                style="max-height: 100px;">
                                                        @elseif (Str::endsWith($salary->payment_majikan_image, ['.pdf']))
                                                            <iframe
                                                                src="{{ route('getFile', ['path' => 'payments', 'fileName' => $salary->payment_majikan_image]) }}"
                                                                width="100%" height="100px"></iframe>
                                                        @else
                                                            <p>Format file tidak didukung untuk preview.</p>
                                                        @endif
                                                    @else
                                                        <p>File tidak ditemukan di server.</p>
                                                    @endif
                                                @else
                                                    Majikan Belum Membayar
                                                @endif
                                            </td>
                                        @endhasrole
                                        @hasrole('superadmin|admin|owner|pembantu')
                                            <td class="text-center">
                                                Rp. {{ number_format($salary->total_salary_pembantu, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($salary->payment_pembantu_image)
                                                    @php
                                                        $filePath = storage_path(
                                                            'app/public/payments/' . $salary->payment_pembantu_image,
                                                        );
                                                    @endphp

                                                    @if (file_exists($filePath))
                                                        @if (Str::endsWith($salary->payment_pembantu_image, ['.jpg', '.jpeg', '.png', '.gif']))
                                                            <img src="{{ route('getFile', ['path' => 'payments', 'fileName' => $salary->payment_pembantu_image]) }}"
                                                                alt="Preview" class="img-fluid zoomable-image"
                                                                style="max-height: 300px;">
                                                        @elseif (Str::endsWith($salary->payment_pembantu_image, ['.pdf']))
                                                            <iframe
                                                                src="{{ route('getFile', ['path' => 'payments', 'fileName' => $salary->payment_pembantu_image]) }}"
                                                                width="100%" height="300px"></iframe>
                                                        @else
                                                            <p>Format file tidak didukung untuk preview.</p>
                                                        @endif
                                                    @else
                                                        <p>File tidak ditemukan di server.</p>
                                                    @endif
                                                @else
                                                    Belum Dibayarkan
                                                @endif
                                            </td>
                                        @endhasrole
                                        @hasrole('superadmin|admin|majikan')
                                            <td class="text-center">
                                                @hasrole('majikan')
                                                    @if (!$salary->payment_majikan_image)
                                                        <a href="#" class="btn btn-sm btn-primary mb-1" data-toggle="modal"
                                                            data-target="#paymentMajikanModal-{{ $salary->id }}"><i
                                                                class="fas fa-money-check-alt"></i></a>
                                                        @include('cms.servant.modal.payment-majikan', [
                                                            'data' => $salary,
                                                        ])

                                                        <a href="#" class="btn btn-sm btn-warning mb-1" data-toggle="modal"
                                                            data-target="#editKehadiranModal-{{ $salary->id }}"><i
                                                                class="fas fa-edit"></i></a>
                                                        @include('cms.servant.modal.edit-presence', [
                                                            'data' => $salary,
                                                        ])
                                                    @endif
                                                @endhasrole

                                                @hasrole('superadmin|admin')
                                                    <a href="#" class="btn btn-sm btn-primary mb-1" data-toggle="modal"
                                                        data-target="#paymentMajikanModal-{{ $salary->id }}"><i
                                                            class="fas fa-money-check-alt"></i></a>
                                                    @include('cms.servant.modal.payment-majikan', [
                                                        'data' => $salary,
                                                    ])

                                                    <a href="#" class="btn btn-sm btn-info mb-1" data-toggle="modal"
                                                        data-target="#paymentAdminModal-{{ $salary->id }}"><i
                                                            class="fas fa-money-bill-wave"></i></a>
                                                    @include('cms.servant.modal.payment-admin', [
                                                        'data' => $salary,
                                                    ])

                                                    <a href="#" class="btn btn-sm btn-warning mb-1" data-toggle="modal"
                                                        data-target="#editKehadiranModal-{{ $salary->id }}"><i
                                                            class="fas fa-edit"></i></a>
                                                    @include('cms.servant.modal.edit-presence', [
                                                        'data' => $salary,
                                                    ])
                                                @endhasrole
                                            </td>
                                        @endhasrole
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
