@extends('cms.layouts.main', ['title' => 'Lowongan Kerja'])

@section('content')
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 text-gray-800">Daftar Lowongan Kerja</h1>
    </div>

    <!-- Card List -->
    <div class="row row-cols-1 row-cols-md-4 g-3 mb-4" id="servantList">
        @foreach ($datas as $data)
            <div class="col-lg-3 mb-3 mb-lg-0">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <a href="{{ route('show-vacancy', $data->id) }}" class="text-secondary">
                            <h5 class="card-title"><strong>{{ $data->title }}</strong></h5>
                        </a>
                        <p class="card-text">
                            <strong>Batas Lamaran:</strong>
                            @php
                                $closingDate = \Carbon\Carbon::parse($data->closing_date);
                                $daysRemaining = $closingDate->diffInDays(now());

                                if ($closingDate->isPast()) {
                                    echo 'Lamaran telah ditutup.';
                                } else {
                                    echo $daysRemaining . ' hari lagi';
                                }
                            @endphp
                        </p>
                        <p class="card-text">{!! \Illuminate\Support\Str::limit($data->description, 100, '...') !!}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
