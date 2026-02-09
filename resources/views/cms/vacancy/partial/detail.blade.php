@extends('cms.layouts.main', ['title' => 'Detail Lowongan'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <h1 class="h3 mb-4 text-gray-800">Detail Lowongan</h1>
        <a href="{{ route('vacancies.index') }}" class="btn btn-secondary"><i class="fas fa-fw fa-arrow-left"></i></a>
    </div>

    <div class="card shadow">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="vacancyTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="detail-tab" data-toggle="tab" href="#detail" role="tab"
                        aria-controls="detail" aria-selected="true">Detail</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="applicant-tab" data-toggle="tab" href="#applicant" role="tab"
                        aria-controls="applicant" aria-selected="false">Pelamar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="applicant-tab" data-toggle="tab" href="#recom" role="tab"
                        aria-controls="applicant" aria-selected="false">Rekomendasi Admin</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="vacancyTabContent">
                <!-- Tab: Detail -->
                <div class="tab-pane fade show active" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                    <h5 class="card-title"><strong>{{ $data->title }}</strong></h5>
                    <p class="card-text"><strong>Batas Lamaran:</strong>
                        {{ \Carbon\Carbon::parse($data->closing_date)->format('d F Y') }} (
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
                    </p>
                    <p class="card-text"><strong>Dibutuhkan:</strong> {{ $data->limit }} Orang</p>
                    <p class="card-text"><strong>Job Desc:</strong> {!! $data->description !!}</p>
                    <p class="card-text"><strong>Spesifikasi:</strong> {!! $data->requirements !!}</p>
                    @if ($data->benefits != null)
                        <p class="card-text"><strong>Keuntungan:</strong> {!! $data->benefits !!}</p>
                    @endif
                </div>

                <!-- Tab: Pelamar -->
                <div class="tab-pane fade" id="applicant" role="tabpanel" aria-labelledby="applicant-tab">
                    @include('cms.vacancy.partial.applicant-tab')
                </div>

                <!-- Tab: Recommendation -->
                <div class="tab-pane fade" id="recom" role="tabpanel" aria-labelledby="recom-tab">
                    @include('cms.vacancy.partial.recom-tab')
                </div>
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
