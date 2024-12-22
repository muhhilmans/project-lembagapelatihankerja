@extends('cms.layouts.main', ['title' => 'Pembantu'])

@section('content')
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 text-gray-800">Daftar Pembantu</h1>
    </div>

    <!-- Filter Section -->
    <div class="card p-4 shadow-sm mb-4">
        <div class="row">
            <!-- Profesi -->
            <div class="col-md-6 mb-3">
                <label for="filterProfession" class="font-weight-bold">Profesi</label>
                <select id="filterProfession" class="form-control">
                    <option value="">Select profesi...</option>
                    @foreach ($professions as $profession)
                        <option value="{{ $profession->name }}">{{ $profession->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Agama -->
            <div class="col-md-6 mb-3">
                <label for="filterReligion" class="font-weight-bold">Agama</label>
                <select id="filterReligion" class="form-control">
                    <option value="">Pilih agama...</option>
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <!-- Usia -->
            <div class="col-md-6 mb-3">
                <label class="font-weight-bold">Usia</label>
                <div class="d-flex align-items-center">
                    <input type="range" id="filterMinAge" class="form-control-range" min="18" max="60" value="19">
                    <span class="ml-2 mr-2">Min <span id="minAgeLabel">19</span> Tahun</span>
                    <input type="range" id="filterMaxAge" class="form-control-range" min="18" max="60" value="45">
                    <span class="ml-2">Max <span id="maxAgeLabel">45</span> Tahun</span>
                </div>
            </div>
            <!-- Pengalaman -->
            <div class="col-md-6 mb-3">
                <label class="font-weight-bold">Pengalaman</label>
                <div class="d-flex align-items-center">
                    <input type="range" id="filterMinExperience" class="form-control-range" min="0" max="40" value="1">
                    <span class="ml-2 mr-2">Min <span id="minExperienceLabel">1</span> Tahun</span>
                    <input type="range" id="filterMaxExperience" class="form-control-range" min="0" max="40" value="40">
                    <span class="ml-2">Max <span id="maxExperienceLabel">45</span> Tahun</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card List -->
    <div class="row row-cols-1 row-cols-md-4 g-3 mb-4" id="servantList">
        @foreach ($datas as $data)
            <div class="col-lg-3 mb-3 mb-lg-0 servant-item"
                data-profession="{{ $data->servantDetails->profession->name }}"
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
        @endforeach
    </div>

    <!-- JavaScript for Filtering -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const servantList = document.getElementById('servantList');
            const filterInputs = document.querySelectorAll('#filterProfession, #filterReligion, #filterMinAge, #filterMaxAge, #filterMinExperience, #filterMaxExperience');

            function applyFilters() {
                const profession = document.getElementById('filterProfession').value.toLowerCase();
                const religion = document.getElementById('filterReligion').value.toLowerCase();
                const minAge = parseInt(document.getElementById('filterMinAge').value);
                const maxAge = parseInt(document.getElementById('filterMaxAge').value);
                const minExperience = parseInt(document.getElementById('filterMinExperience').value);
                const maxExperience = parseInt(document.getElementById('filterMaxExperience').value);

                const items = Array.from(servantList.getElementsByClassName('servant-item'));
                items.forEach(item => {
                    const matchesProfession = profession ? item.dataset.profession.toLowerCase().includes(profession) : true;
                    const matchesReligion = religion ? item.dataset.religion.toLowerCase().includes(religion) : true;
                    const age = parseInt(item.dataset.age);
                    const matchesAge = age >= minAge && age <= maxAge;
                    const experience = parseInt(item.dataset.experience);
                    const matchesExperience = experience >= minExperience && experience <= maxExperience;

                    item.style.display = matchesProfession && matchesReligion && matchesAge && matchesExperience ? '' : 'none';
                });
            }

            filterInputs.forEach(input => input.addEventListener('input', applyFilters));

            const minAgeLabel = document.getElementById('minAgeLabel');
            const maxAgeLabel = document.getElementById('maxAgeLabel');
            document.getElementById('filterMinAge').addEventListener('input', function () {
                minAgeLabel.textContent = this.value;
            });
            document.getElementById('filterMaxAge').addEventListener('input', function () {
                maxAgeLabel.textContent = this.value;
            });

            const minExperienceLabel = document.getElementById('minExperienceLabel');
            const maxExperienceLabel = document.getElementById('maxExperienceLabel');
            document.getElementById('filterMinExperience').addEventListener('input', function () {
                minExperienceLabel.textContent = this.value;
            });
            document.getElementById('filterMaxExperience').addEventListener('input', function () {
                maxExperienceLabel.textContent = this.value;
            });
        });
    </script>
@endsection
