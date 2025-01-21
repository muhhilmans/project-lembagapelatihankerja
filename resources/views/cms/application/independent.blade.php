@extends('cms.layouts.main', ['title' => 'Lamaran Kerja'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Lamaran - Mandiri</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Lowongan</th>
                            <th>Nama Majikan</th>
                            @hasrole('superadmin|admin')
                                <th>Nama Pelamar</th>
                            @endhasrole
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th>Gaji Pokok</th>
                            @hasrole('superadmin|pembantu')
                                @if ($datas->contains(fn($data) => in_array($data->status, ['passed', 'accepted'])))
                                    <th>Aksi</th>
                                @endif
                            @endhasrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->vacancy->title }}</td>
                                <td>{{ $data->vacancy->user->name }}</td>
                                @hasrole('superadmin|admin')
                                    <td>{{ $data->servant->name }}</td>
                                @endhasrole
                                <td class="text-center">
                                    @if ($data->status === 'schedule' || $data->status === 'interview')
                                        {{ \Carbon\Carbon::parse($data->interview_date ? $data->interview_date : '')->format('d-M-Y') }}
                                    @elseif ($data->status === 'accepted')
                                        {{ \Carbon\Carbon::parse($data->work_start_date ? $data->work_start_date : '')->format('d-M-Y') }}
                                    @elseif ($data->status === 'rejected' || $data->status === 'laidoff')
                                        {{ \Carbon\Carbon::parse($data->updated_at ? $data->updated_at : '')->format('d-M-Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                @if ($data->status === 'interview')
                                    <td class="text-center">{!! $data->notes_interview !!}</td>
                                @elseif ($data->status === 'verify')
                                    <td class="text-center">{!! $data->notes_verify !!}</td>
                                @elseif ($data->status === 'rejected' || $data->status === 'laidoff')
                                    <td class="text-center">{!! $data->notes_rejected !!}</td>
                                @else
                                    <td class="text-center">-</td>
                                @endif
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
                                            default => 'Status Tidak Diketahui',
                                        } }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{-- @php
                                        $salary = $data->salary;
                                        $service = $salary * 0.025;
                                        $gaji = $salary - $service;
                                    @endphp --}}

                                    Rp. {{ number_format($data->salary, 0, ',', '.') }}
                                </td>

                                @hasrole('superadmin|pembantu')
                                    <td class="text-center">
                                        @if ($data->status === 'passed')
                                            <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                                data-target="#passedModal-{{ $data->id }}"><i
                                                    class="fas fa-building"></i></a>
                                            @include('cms.application.modal.passed', [
                                                'data' => $data,
                                            ])
                                        @endif

                                        @if ($data->status === 'accepted')
                                            @php
                                                $hasComplaintWithSameServant = $data->complaint->contains(function (
                                                    $complaint,
                                                ) use ($data) {
                                                    return $complaint->servant_id == $data->servant_id;
                                                });
                                            @endphp

                                            @if (!$hasComplaintWithSameServant)
                                                <a href="#" class="btn btn-sm btn-danger mr-1" data-toggle="modal"
                                                    data-target="#complaintModal-{{ $data->id }}">
                                                    <i class="fas fa-bullhorn"></i>
                                                </a>
                                                @include('cms.application.modal.complaint', [
                                                    'data' => $data,
                                                ])
                                            @endif
                                        @endif
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
