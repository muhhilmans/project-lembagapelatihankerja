@extends('cms.layouts.main', ['title' => 'Pekerja'])

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
                            @hasrole('superadmin|admin|owner|majikan')
                                <th>Nama Pembantu</th>
                            @endhasrole
                            @hasrole('superadmin|admin|owner|pembantu')
                                <th>Nama Majikan</th>
                            @endhasrole
                            <th>Gaji Pokok</th>
                            <th>Tanggal Bekerja</th>
                            {{-- @hasrole('majikan') --}}
                            @hasrole('superadmin|admin')
                                <th>Gaji (Dengan Tambahan)</th>
                            @endhasrole
                            {{-- @hasrole('superadmin|admin|owner|pembantu') --}}
                            @hasrole('superadmin|admin')
                                <th>Gaji (Dengan Potongan)</th>
                            @endhasrole
                            <th>Status</th>
                            @hasrole('superadmin|admin|owner')
                                <th>Bank</th>
                            @endhasrole
                            @hasrole('superadmin|admin|owner')
                                <th>BPJS</th>
                            @endhasrole
                            @hasrole('superadmin|admin|owner')
                                <th>Aksi</th>
                            @endhasrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                @hasrole('superadmin|admin|owner|majikan')
                                    <td>{{ $data->servant->name }}</td>
                                @endhasrole
                                @hasrole('superadmin|admin|owner|pembantu')
                                    <td>
                                        @if ($data->vacancy_id != null)
                                            {{ $data->vacancy->user->name }}
                                        @else
                                            {{ $data->employe->name }}
                                        @endif
                                    </td>
                                @endhasrole
                                <td class="text-center">Rp. {{ number_format($data->salary, 0, ',', '.') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($data->work_start_date)->format('d-M-Y') }}
                                </td>
                                {{-- @hasrole('majikan') --}}
                                @hasrole('superadmin|admin')
                                    <td class="text-center">
                                        @php
                                            $workerSalaries = App\Models\WorkerSalary::where(
                                                'application_id',
                                                $data->id,
                                            )->get();
                                            $currentMonth = \Carbon\Carbon::now()->format('F Y');

                                            $workerSalary = 'Belum Mengisi Kehadiran';

                                            foreach ($workerSalaries as $salary) {
                                                if (
                                                    \Carbon\Carbon::parse($salary->month)->format('F Y') ==
                                                    $currentMonth
                                                ) {
                                                    $workerSalary =
                                                        'Rp. ' .
                                                        number_format($salary->total_salary_majikan, 0, ',', '.');
                                                    break;
                                                }
                                            }
                                        @endphp

                                        @if (strpos($workerSalary, 'Rp.') !== false)
                                            {!! $workerSalary !!}
                                        @else
                                            {{ $workerSalary }}
                                        @endif
                                    </td>
                                @endhasrole
                                {{-- @hasrole('superadmin|admin|owner|pembantu') --}}
                                @hasrole('superadmin|admin')
                                    <td class="text-center">
                                        @php
                                            $workerSalaries = App\Models\WorkerSalary::where(
                                                'application_id',
                                                $data->id,
                                            )->get();
                                            $currentMonth = \Carbon\Carbon::now()->format('F Y');

                                            $workerSalary = 'Belum Mengisi Kehadiran';

                                            foreach ($workerSalaries as $salary) {
                                                if (
                                                    \Carbon\Carbon::parse($salary->month)->format('F Y') ==
                                                    $currentMonth
                                                ) {
                                                    $workerSalary =
                                                        'Rp. ' .
                                                        number_format($salary->total_salary_pembantu, 0, ',', '.');
                                                    break;
                                                }
                                            }
                                        @endphp

                                        @if (strpos($workerSalary, 'Rp.') !== false)
                                            {!! $workerSalary !!}
                                        @else
                                            {{ $workerSalary }}
                                        @endif
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
                                {{-- @hasrole('majikan')
                                    <td class="text-center">
                                        123123123 (BCA)
                                    </td>
                                @endhasrole --}}
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
                                @hasrole('superadmin|admin|owner')
                                    <td class="text-center">
                                        @hasrole('superadmin|admin')
                                            <a href="{{ route('worker.show', $data->id) }}" class="btn btn-sm btn-info mb-1"><i
                                                    class="fas fa-eye"></i></a>
                                        @endhasrole
                                        @hasrole('superadmin|admin|majikan')
                                            @hasrole('superadmin|admin')
                                                <a href="#" class="btn btn-sm btn-warning mb-1" data-toggle="modal"
                                                    data-target="#editSchemaModal-{{ $data->id }}"><i class="fas fa-edit"></i></a>
                                                @include('cms.servant.modal.schema', [
                                                    'data' => $data,
                                                ])

                                                @if ($data->servant->servantDetails->is_bank == 0 || $data->servant->servantDetails->is_bpjs == 0)
                                                    <a href="#" class="btn btn-sm btn-secondary mb-1" data-toggle="modal"
                                                        data-target="#editBankModal-{{ $data->id }}"><i
                                                            class="fas fa-money-check"></i></a>
                                                    @include('cms.servant.modal.edit-bank', [
                                                        'data' => $data,
                                                    ])
                                                @endif

                                                @if ($data->status == 'review')
                                                    <a href="#" class="btn btn-sm btn-success mb-1" data-toggle="modal"
                                                        data-target="#laidoffModal-{{ $data->id }}"><i
                                                            class="fas fa-check"></i></a>
                                                    @include('cms.servant.modal.laidoff', [
                                                        'data' => $data,
                                                    ])

                                                    <a href="#" class="btn btn-sm btn-danger mb-1" data-toggle="modal"
                                                        data-target="#rejectModal-{{ $data->id }}"><i class="fas fa-times"></i></a>
                                                    @include('cms.servant.modal.reject', ['data' => $data])
                                                @endif
                                            @endhasrole

                                            @if ($data->status == 'accepted')
                                                <a href="#" class="btn btn-sm btn-danger mb-1" data-toggle="modal"
                                                    data-target="#reviewModal-{{ $data->id }}"><i
                                                        class="fas fa-user-times"></i></a>
                                                @include('cms.servant.modal.review', ['data' => $data])
                                            @endif
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
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/summernote/summernote-bs4.min.css') }}">
@endpush

@push('custom-script')
    <script src="{{ asset('assets/vendor/summernote/summernote-bs4.min.js') }}"></script>
@endpush
