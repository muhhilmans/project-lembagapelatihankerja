<div class="col-lg-3 mb-3 mb-lg-0 servant-item" data-profession="{{ $data->servantDetails->profession->name }}"
    data-age="{{ \Carbon\Carbon::parse($data->servantDetails->date_of_birth)->age }}"
    data-religion="{{ $data->servantDetails->religion }}" data-experience="{{ $data->servantDetails->experience }}">
    <div class="card shadow-sm h-100">
        <!-- Photo -->
        @if ($data->servantDetails->photo)
            <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $data->servantDetails->photo]) }}"
                class="card-img-top img-fluid" alt="Pembantu {{ $data->name }}">
        @else
            <img src="{{ asset('assets/img/undraw_rocket.svg') }}" class="card-img-top img-fluid p-3"
                alt="Pembantu {{ $data->name }}">
        @endif

        <!-- Card Content -->
        <div class="card-body">
            <ul class="list-unstyled mb-3">
                <li class="mb-2">
                    <i class="fas fa-user"></i>
                    <strong>Nama:</strong> {{ $data->name }}
                </li>
                <li class="mb-2">
                    <i class="fas fa-calendar-alt"></i>
                    <strong>Usia:</strong>
                    {{ \Carbon\Carbon::parse($data->servantDetails->date_of_birth)->age }} Tahun
                </li>
                <li class="mb-2">
                    <i class="fas fa-praying-hands"></i>
                    <strong>Agama:</strong> {{ $data->servantDetails->religion }}
                </li>
                <li class="mb-2">
                    <i class="fas fa-user-tie"></i>
                    <strong>Profesi:</strong> {{ $data->servantDetails->profession->name }}
                </li>
                <li>
                    <i class="fas fa-briefcase"></i>
                    <strong>Pengalaman:</strong> {{ $data->servantDetails->experience }}
                </li>
            </ul>
            <p class="card-text text-muted">
                {{ $data->servantDetails->description == '-' ? 'Belum ada deskripsi' : \Illuminate\Support\Str::limit($data->servantDetails->description, 100, '...') }}
            </p>
        </div>

        <!-- Card Footer -->
        <div class="card-footer text-right">
            <a class="btn btn-sm btn-info" href="{{ route('show-servant', $data->id) }}">
                <i class="fas fa-eye"></i> Detail
            </a>
        </div>
    </div>
</div>
