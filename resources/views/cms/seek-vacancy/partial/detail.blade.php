@extends('cms.layouts.main', ['title' => 'Detail Lowongan Kerja'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline mb-4 mb-lg-0">
        <h1 class="h3 mb-4 text-gray-800">Detail Lowongan Kerja</h1>
        <div class="d-flex flex-column flex-lg-row">
            @if (auth()->user()->hasRole('pembantu'))
                @php
                    $hasApplied = $data->applications->contains(function ($job) use ($data) {
                        return $job->servant_id === auth()->user()->id && $job->vacancy_id === $data->id;
                    });

                    // Cek apakah pekerja terikat kontrak aktif
                    $hasActiveContract = \App\Models\Application::where('servant_id', auth()->user()->id)
                        ->where('status', 'accepted')
                        ->where('salary_type', 'contract')
                        ->exists();
                @endphp

                @if ($hasApplied)
                    <button class="btn btn-secondary mr-0 mr-lg-1 mb-1 mb-lg-0" disabled>
                        <i class="fas fa-fw fa-check"></i> Sudah Melamar
                    </button>
                @elseif ($hasActiveContract)
                    <button class="btn btn-secondary mr-0 mr-lg-1 mb-1 mb-lg-0" disabled
                        title="Anda sedang terikat kontrak dan tidak dapat melamar lowongan baru">
                        <i class="fas fa-fw fa-ban"></i> Terikat Kontrak
                    </button>
                @else
                    <a href="#" class="btn btn-primary mr-0 mr-lg-1 mb-1 mb-lg-0" data-toggle="modal"
                        data-target="#applyModal-{{ $data->id }}">
                        <i class="fas fa-fw fa-check"></i> Lamar
                    </a>
                    @include('cms.seek-vacancy.modal.apply', ['vacancy' => $data])
                @endif
            @elseif(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                <a href="#" class="btn btn-primary mr-2" data-toggle="modal" data-target="#recommendModal-{{ $data->id }}">Rekomendasi</a>
                @include('cms.seek-vacancy.modal.recommend', ['vacancy' => $data])
            @endif

            <a href="{{ route('all-vacancy') }}" class="btn btn-secondary"><i class="fas fa-fw fa-arrow-left"></i></a>
        </div>
    </div>

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
                        $startOfDayUser = now()->startOfDay();
                        $startOfDayClosing = $closingDate->startOfDay();
                        $diff = $startOfDayUser->diffInDays($startOfDayClosing, false);

                        if ($diff < 0) {
                            echo 'Lamaran telah ditutup.';
                        } elseif ($diff == 0) {
                            echo 'Hari ini terakhir.';
                        } else {
                            echo $diff . ' hari lagi';
                        }
                    @endphp
                    )
                </li>
            </ul>
            </p>
            <p class="card-text"><strong>Job Desc:</strong> {!! $data->description !!}</p>
            <p class="card-text"><strong>Spesifikasi:</strong> {!! $data->requirements !!}</p>
            @if ($data->benefits != null)
                <p class="card-text"><strong>Keuntungan:</strong> {!! $data->benefits !!}</p>
            @endif
        </div>
    </div>

    {{-- ULASAN & RATING SECTION --}}
    <style>
        .review-card {
            border-radius: 12px;
            border: 1px solid #e3e6f0;
            background-color: #f8f9fc;
            padding: 1rem;
            height: 100%;
            transition: all 0.3s ease;
        }
        .review-card:hover {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transform: translateY(-2px);
        }
        .review-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4e73df;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
    <div class="card shadow mb-3 p-4" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-star fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="font-weight-bold text-dark mb-0">Ulasan & Rating Majikan</h5>
                        <div class="text-warning mt-1">
                            <span class="font-weight-bold" style="font-size: 1.2rem;">{{ $data->user->average_rating > 0 ? $data->user->average_rating : '0.0' }}</span>
                            <small class="text-muted ml-1">/ 5 ({{ $data->user->review_count }} Ulasan)</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    @if($data->user->receivedReviews->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-comment-slash fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Belum ada ulasan untuk majikan ini.</p>
                        </div>
                    @else
                        <div class="row">
                            @foreach($data->user->receivedReviews as $review)
                                <div class="col-md-6 mb-3">
                                    <div class="review-card">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="review-avatar mr-2 shadow-sm">
                                                    {{ strtoupper(substr($review->reviewer->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold text-dark">{{ $review->reviewer->name }}</h6>
                                                    <small class="text-muted"><i class="fas fa-user-tag mr-1"></i>{{ ucfirst($review->reviewer->roles->first()->name ?? 'User') }}</small>
                                                </div>
                                            </div>
                                            <small class="text-muted text-right">{{ $review->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-gray-300' }}"></i>
                                            @endfor
                                        </div>
                                        <p class="mb-0 text-dark font-italic">"{{ $review->comment }}"</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
