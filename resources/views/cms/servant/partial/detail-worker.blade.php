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
                                    $service = $salary * 0.075;
                                    $gaji = $salary + $service;
                                @endphp

                                Rp. {{ number_format($gaji, 0, ',', '.') }}
                            @endhasrole

                            @hasrole('superadmin|admin|owner|pembantu')
                                @php
                                    $salary = $data->salary;
                                    $service = $salary * 0.025;
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
                                    <th>Bulan</th>
                                    <th>Hadir</th>
                                    @hasrole('superadmin|admin|owner|majikan')
                                        <th>Gaji (Dengan Tambahan 7,5%)</th>
                                        <th>Bukti Pembayaran</th>
                                    @endhasrole
                                    @hasrole('superadmin|admin|owner|pembantu')
                                        <th>Gaji (Dengan Potongan 2,5%)</th>
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
                                        <td>{{ \Carbon\Carbon::parse($salary->month)->format('F Y') }}</td>
                                        <td class="text-center">
                                            {{ $salary->presence }} Hari
                                        </td>
                                        @hasrole('superadmin|admin|owner|majikan')
                                            <td class="text-center">
                                                Rp. {{ number_format($salary->total_salary_majikan, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($salary->payment_proof)
                                                    <a href="{{ route('getImage', ['path' => 'payment_proof', 'imageName' => $salary->payment_proof]) }}"
                                                        target="_blank">
                                                        <img src="{{ route('getImage', ['path' => 'payment_proof', 'imageName' => $salary->payment_proof]) }}"
                                                            class="img-fluid rounded mx-auto d-block zoomable-image"
                                                            style="max-height: 100px;" alt="...">
                                                    </a>
                                                @else
                                                    <img src="{{ asset('assets/img/undraw_rocket.svg') }}"
                                                        class="img-fluid rounded mx-auto d-block zoomable-image"
                                                        style="max-height: 100px;" alt="...">
                                                @endif
                                            </td>
                                        @endhasrole
                                        @hasrole('superadmin|admin|owner|pembantu')
                                            <td class="text-center">
                                                Rp. {{ number_format($salary->total_salary_pembantu, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($salary->paid_proof)
                                                    <a href="{{ route('getImage', ['path' => 'paid_proof', 'imageName' => $salary->paid_proof]) }}"
                                                        target="_blank">
                                                        <img src="{{ route('getImage', ['path' => 'paid_proof', 'imageName' => $salary->paid_proof]) }}"
                                                            class="img-fluid rounded mx-auto d-block zoomable-image"
                                                            style="max-height: 100px;" alt="...">
                                                    </a>
                                                @else
                                                    <img src="{{ asset('assets/img/undraw_rocket.svg') }}"
                                                        class="img-fluid rounded mx-auto d-block zoomable-image"
                                                        style="max-height: 100px;" alt="...">
                                                @endif
                                            </td>
                                        @endhasrole
                                        @hasrole('superadmin|admin|majikan')
                                            <td class="text-center">
                                                <a href="#" class="btn btn-sm btn-primary mb-1"><i
                                                        class="fas fa-money-check-alt"></i></a>

                                                @hasrole('superadmin|admin')
                                                    <a href="#" class="btn btn-sm btn-info mb-1"><i
                                                            class="fas fa-money-bill-wave"></i></a>
                                                @endhasrole
                                                
                                                <a href="#" class="btn btn-sm btn-warning mb-1"><i
                                                    class="fas fa-edit"></i></a>
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
