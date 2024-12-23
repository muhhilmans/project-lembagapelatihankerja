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
                    <a class="nav-link active" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="true">Detail</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="applicant-tab" data-toggle="tab" href="#applicant" role="tab" aria-controls="applicant" aria-selected="false">Pelamar</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="vacancyTabContent">
                <!-- Tab: Detail -->
                <div class="tab-pane fade show active" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                    <h5 class="card-title">{{ $data->title }}</h5>
                    <p class="card-text"><strong>Batas Lamaran:</strong> {{ $data->closing_date }}</p>
                    <p class="card-text"><strong>Deskripsi:</strong> {!! $data->description !!}</p>
                    <p class="card-text"><strong>Spesifikasi:</strong> {!! $data->requirements !!}</p>
                    <p class="card-text"><strong>Keuntungan:</strong> {!! $data->benefits !!}</p>
                </div>

                <!-- Tab: Link -->
                <div class="tab-pane fade" id="applicant" role="tabpanel" aria-labelledby="applicant-tab">
                    <h5 class="card-title">Tautan Terkait</h5>
                    <p class="card-text">Berikut adalah tautan yang berkaitan dengan lowongan ini:</p>
                </div>
            </div>
        </div>
    </div>
@endsection
