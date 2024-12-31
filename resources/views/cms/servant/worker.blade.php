@extends('cms.layouts.main', ['title' => 'Pekerja'])

@section('content')
    <!-- Page Heading -->
    <div class="mb-4 d-flex justify-content-between align-items-baseline">
        <h1 class="h3 text-gray-800">Daftar Pembantu Bekerja</h1>
        @hasrole('superadmin|admin|owner')
            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#downloadModal"><i
                    class="fas fa-download"></i></a>
            @include('cms.servant.modal.export')
        @endhasrole
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Pembantu</th>
                            @hasrole('superadmin|admin|owner')
                                <th>Nama Majikan</th>
                            @endhasrole
                            <th>Tanggal Bekerja</th>
                            @hasrole('majikan')
                                <th>Gaji (Dengan Tambahan 7,5%)</th>
                            @endhasrole
                            @hasrole('superadmin|admin|owner|pembantu')
                                <th>Gaji (Dengan Potongan 2,5%)</th>
                            @endhasrole
                            <th>Status</th>
                            <th>Bank</th>
                            @hasrole('superadmin|admin|owner')
                                <th>BPJS</th>
                            @endhasrole
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->servant->name }}</td>
                                @hasrole('superadmin|admin|owner')
                                    <td>
                                        @if ($data->vacancy_id != null)
                                            {{ $data->vacancy->user->name }}
                                        @else
                                            {{ $data->employe->name }}
                                        @endif
                                    </td>
                                @endhasrole
                                <td class="text-center">{{ \Carbon\Carbon::parse($data->work_start_date)->format('d-M-Y') }}
                                </td>
                                @hasrole('majikan')
                                    <td class="text-center">
                                        @php
                                            $salary = $data->salary;
                                            $service = $salary * 0.075;
                                            $gaji = $salary + $service;
                                        @endphp

                                        Rp. {{ number_format($gaji, 0, ',', '.') }}
                                    </td>
                                @endhasrole
                                @hasrole('superadmin|admin|owner|pembantu')
                                    <td class="text-center">
                                        @php
                                            $salary = $data->salary;
                                            $service = $salary * 0.025;
                                            $gaji = $salary - $service;
                                        @endphp

                                        Rp. {{ number_format($gaji, 0, ',', '.') }}
                                    </td>
                                @endhasrole
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
                                            default => 'Review',
                                        } }}
                                    </span>
                                </td>
                                @hasrole('majikan')
                                    <td class="text-center">
                                        123123123 (BCA)
                                    </td>
                                @endhasrole
                                @hasrole('superadmin|admin|owner')
                                    <td class="text-center">
                                        @if ($data->servant->servantDetails->is_bank == 1)
                                            {{ $data->servant->servantDetails->account_number }}
                                            ({{ $data->servant->servantDetails->bank_name }})
                                        @else
                                            Belum memiliki rekening
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($data->servant->servantDetails->is_bpjs == 1)
                                            {{ $data->servant->servantDetails->number_bpjs }}
                                            ({{ $data->servant->servantDetails->type_bpjs }})
                                        @else
                                            Belum memiliki BPJS
                                        @endif
                                    </td>
                                @endhasrole
                                <td class="text-center">
                                    @hasrole('superadmin|admin')
                                        @if ($data->servant->servantDetails->is_bank == 0 || $data->servant->servantDetails->is_bpjs == 0)
                                            <a href="#" class="btn btn-warning mb-2" data-toggle="modal"
                                                data-target="#editBankModal-{{ $data->id }}"><i
                                                    class="fas fa-edit"></i></a>
                                            @include('cms.servant.modal.edit-bank', ['data' => $data])
                                        @endif

                                        @if ($data->status == 'review')
                                            <a href="#" class="btn btn-success mb-2" data-toggle="modal"
                                                data-target="#laidoffModal-{{ $data->id }}"><i
                                                    class="fas fa-check"></i></a>
                                            @include('cms.servant.modal.laidoff', ['data' => $data])

                                            <a href="#" class="btn btn-danger" data-toggle="modal"
                                                data-target="#rejectModal-{{ $data->id }}"><i class="fas fa-times"></i></a>
                                            @include('cms.servant.modal.reject', ['data' => $data])
                                        @endif
                                    @endhasrole

                                    @if ($data->status == 'accepted')
                                        <a href="#" class="btn btn-danger" data-toggle="modal"
                                            data-target="#reviewModal-{{ $data->id }}"><i
                                                class="fas fa-user-times"></i></a>
                                        @include('cms.servant.modal.review', ['data' => $data])
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/summernote/summernote-bs4.min.css') }}">
@endpush

@push('custom-script')
    <script src="{{ asset('assets/vendor/summernote/summernote-bs4.min.js') }}"></script>
@endpush
