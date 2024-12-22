@extends('cms.layouts.main', ['title' => 'Pembantu'])

@section('content')
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 text-gray-800">Daftar Pembantu</h1>
    </div>

    <!-- Sorting Dropdown -->
    <div class="mb-4">
        <div class="card p-3 shadow-sm">
            <div class="form-group">
                <label for="sortBy" class="font-weight-bold">
                    <i class="fas fa-sort"></i> Urutkan berdasarkan:
                </label>
                <select id="sortBy" class="form-control" multiple>
                    <option value="name">Nama</option>
                    <option value="age">Umur</option>
                    <option value="religion">Agama</option>
                    <option value="experience">Pengalaman</option>
                </select>
                <small class="form-text text-muted mt-1">
                    Pilih lebih dari satu kriteria dengan menahan tombol <kbd>Ctrl</kbd> (Windows) atau <kbd>Command</kbd>
                    (Mac).
                </small>
            </div>
        </div>
    </div>

    <!-- Card List -->
    <div class="row row-cols-1 row-cols-md-4 g-3 mb-4" id="servantList">
        @foreach ($datas as $data)
            <div class="col servant-item" data-name="{{ $data->name }}"
                data-age="{{ \Carbon\Carbon::parse($data->servantDetails->date_of_birth)->age }}"
                data-religion="{{ $data->servantDetails->religion }}"
                data-experience="{{ $data->servantDetails->experience }}">
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
                                <strong>Umur:</strong>
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
                    <div class="card-footer text-center">
                        <a class="btn btn-sm btn-info" href="{{ route('show-servant', $data->id) }}">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('custom-script')
    <script>
        document.getElementById('sortBy').addEventListener('change', function() {
            const selectedOptions = Array.from(this.selectedOptions).map(option => option.value);
            const servantList = document.getElementById('servantList');
            const items = Array.from(servantList.getElementsByClassName('servant-item'));

            items.sort((a, b) => {
                for (const criteria of selectedOptions) {
                    const valA = a.dataset[criteria].toLowerCase();
                    const valB = b.dataset[criteria].toLowerCase();

                    if (criteria === 'age' || criteria === 'experience') {
                        const diff = parseInt(valA) - parseInt(valB);
                        if (diff !== 0) return diff;
                    } else {
                        const diff = valA.localeCompare(valB);
                        if (diff !== 0) return diff;
                    }
                }
                return 0;
            });

            items.forEach(item => servantList.appendChild(item));
        });
    </script>
@endpush
