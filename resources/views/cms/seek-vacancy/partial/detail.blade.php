@extends('cms.layouts.main', ['title' => 'Detail Lowongan Kerja'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline mb-4 mb-lg-0">
        <h1 class="h3 mb-4 text-gray-800">Detail Lowongan Kerja</h1>
        <div class="d-flex flex-column flex-lg-row">
            @php
                $hasApplied = $data->applyJobs->contains(function ($job) use ($data) {
                    return $job->servant_id === auth()->user()->id && $job->vacancy_id === $data->id;
                });
            @endphp

            @if ($hasApplied)
                <button class="btn btn-secondary mr-0 mr-lg-1 mb-1 mb-lg-0" disabled>
                    <i class="fas fa-fw fa-check"></i> Sudah Melamar
                </button>
            @else
                <a href="#" class="btn btn-primary mr-0 mr-lg-1 mb-1 mb-lg-0" data-toggle="modal"
                    data-target="#applyModal-{{ $data->id }}">
                    <i class="fas fa-fw fa-check"></i> Lamar
                </a>
                @include('cms.seek-vacancy.modal.apply', ['vacancy' => $data])
            @endif



            <a href="{{ route('all-vacancy') }}" class="btn btn-secondary"><i class="fas fa-fw fa-arrow-left"></i></a>
        </div>
    </div>

    @if (session('error'))
        <h5 class="text-danger">{{ session('error') }}</h5>
    @endif

    @if (session('success'))
        <h5 class="text-success">{{ session('success') }}</h5>
    @endif

    <div class="card shadow mb-3 p-3">
        <div class="card-body">
            <h5 class="card-title"><strong>{{ $data->title }}</strong></h5>
            <hr>
            <p class="card-text">
            <ul class="list-unstyled">
                <li><i class="fas fa-fw fa-user"></i> {{ $data->user->name }}</li>
                <li>
                    <i class="fas fa-fw fa-clock"></i> {{ \Carbon\Carbon::parse($data->closing_date)->format('d F Y') }} (
                    @php
                        $closingDate = \Carbon\Carbon::parse($data->closing_date);
                        $daysRemaining = $closingDate->diffInDays(now());

                        if ($closingDate->isPast()) {
                            echo 'Lamaran telah ditutup.';
                        } else {
                            echo $daysRemaining . ' hari lagi';
                        }
                    @endphp
                    )
                </li>
            </ul>
            </p>
            <p class="card-text"><strong>Deskripsi:</strong> {!! $data->description !!}</p>
            <p class="card-text"><strong>Spesifikasi:</strong> {!! $data->requirements !!}</p>
            @if ($data->benefits != null)
                <p class="card-text"><strong>Keuntungan:</strong> {!! $data->benefits !!}</p>
            @endif
        </div>
    </div>
@endsection
