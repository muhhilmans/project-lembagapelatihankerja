@extends('cms.layouts.main', ['title' => 'Pembantu'])

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Daftar Pembantu</h1>

    <!-- Card -->
    <div class="row">
        @foreach ($datas as $data)
            <div class="col-lg-3">
                <div class="card shadow">
                    @if ($data->servantDetails->photo)
                    <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $data->servantDetails->photo]) }}"
                        class="card-img-top img-fluid rounded mx-auto d-block zoomable-image" style="max-height: 150px; width: auto;" alt="Pembantu {{ $data->name }}">
                @else
                    <img src="{{ asset('assets/img/undraw_rocket.svg') }}"
                        class="card-img-top img-fluid rounded mx-auto d-block zoomable-image p-3" style="max-height: 150px; width: auto;" alt="Pembantu {{ $data->name }}">
                @endif
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-1"><i class="fas fa-user"></i> Nama: {{ $data->name }}</li>
                            <li class="mb-1"><i class="fas fa-calendar-alt"></i> Umur: {{ \Carbon\Carbon::parse($data->servantDetails->date_of_birth)->age }} Tahun
                            </li>
                            <li class="mb-1"><i class="fas fa-praying-hands"></i> Agama: {{ $data->servantDetails->religion }}
                            </li>
                            <li><i class="fas fa-user-tie"></i> Profesi: {{ $data->servantDetails->profession->name }}</li>
                            <li class="mb-1"><i class="fas fa-briefcase"></i> Pengalaman: {{ $data->servantDetails->experience }}
                            </li>
                        </ul>

                        <p class="card-text">{{ $data->servantDetails->description == '-' ? 'Belum ada deskripsi' : \Illuminate\Support\Str::limit($data->servantDetails->description, 100, '...') }}</p>
                    </div>
                    <div class="card-footer">
                        <a class="btn btn-sm btn-info" href="{{ route('show-servant', $data->id) }}"><i
                            class="fas fa-eye"></i> Detail</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
