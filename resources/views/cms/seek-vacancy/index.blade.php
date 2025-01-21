@extends('cms.layouts.main', ['title' => 'Lowongan Kerja'])

@section('content')
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 text-gray-800">Daftar Lowongan Kerja</h1>
    </div>

    <div class="card p-4 shadow-sm mb-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="searchInput" class="font-weight-bold">Cari Lowongan</label>
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Masukkan judul lowongan / nama majikan...">
            </div>

            <div class="col-md-6 mb-3">
                <label for="filterProfession" class="font-weight-bold">Profesi</label>
                <select id="filterProfession" class="form-control">
                    <option value="">Pilih profesi...</option>
                    @foreach ($professions as $profession)
                        <option value="{{ $profession->id }}">{{ $profession->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Card List -->
    <div class="row mb-4" id="vacancyList">
        @if ($datas->isEmpty())
            <div class="col-12 text-center">
                <p class="text-muted text-center">Belum ada lowongan kerja</p>
            </div>
        @else
            @foreach ($datas as $data)
                <div class="col-lg-3 mb-3 mb-lg-0 vacancy-card" data-profession-id="{{ $data->profession_id }}"
                    data-title="{{ strtolower($data->title) }}" data-employer="{{ strtolower($data->user->name) }}">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <a href="{{ route('show-vacancy', $data->id) }}" class="text-secondary">
                                <h5 class="card-title"><strong>{{ $data->title }}</strong></h5>
                            </a>
                            <p class="card-text">
                                <strong>Majikan:</strong> {{ $data->user->name }}
                            </p>
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
        @endif
    </div>
@endsection

@push('custom-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterProfession = document.getElementById('filterProfession');
            const searchInput = document.getElementById('searchInput');
            const vacancyList = document.getElementById('vacancyList');
            const vacancyCards = document.querySelectorAll('.vacancy-card');

            function applyFilters() {
                const selectedProfessionId = filterProfession.value;
                const searchValue = searchInput.value.trim().toLowerCase();

                vacancyList.innerHTML = '';
                let hasVisibleCards = false;

                vacancyCards.forEach(card => {
                    const cardProfessionId = card.getAttribute('data-profession-id');
                    const cardTitle = card.getAttribute('data-title');
                    const cardEmployer = card.getAttribute('data-employer');

                    const matchesProfession = !selectedProfessionId || String(cardProfessionId) === String(
                        selectedProfessionId);
                    const matchesSearch = !searchValue || cardTitle.includes(searchValue) || cardEmployer
                        .includes(searchValue);

                    if (matchesProfession && matchesSearch) {
                        card.style.display = '';
                        vacancyList.appendChild(card);
                        hasVisibleCards = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (!hasVisibleCards) {
                    vacancyList.innerHTML = `
                    <div class="col-12 text-center">
                        <p class="text-muted text-center">Belum ada lowongan kerja</p>
                    </div>`;
                }
            }

            filterProfession.addEventListener('change', applyFilters);
            searchInput.addEventListener('input', applyFilters);
        });
    </script>
@endpush
