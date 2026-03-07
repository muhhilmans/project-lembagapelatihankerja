@extends('cms.layouts.main', ['title' => 'Lowongan Kerja'])

@section('content')
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 text-gray-800">Daftar Lowongan Kerja</h1>
    </div>

    <div class="card p-4 shadow-sm mb-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="searchInput" class="font-weight-bold">Cari Lowongan</label>
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Masukkan judul lowongan / nama majikan...">
            </div>

            <div class="col-md-4 mb-3">
                <label for="filterProfession" class="font-weight-bold">Profesi</label>
                <select id="filterProfession" class="form-control">
                    <option value="">Pilih profesi...</option>
                    @foreach ($professions as $profession)
                        <option value="{{ $profession->id }}">{{ $profession->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Rating -->
            <div class="col-md-4 mb-3">
                <label for="filterMinRating" class="font-weight-bold">Minimal Rating</label>
                <select id="filterMinRating" class="form-control">
                    <option value="0">Semua Rating</option>
                    <option value="5">Rating 5</option>
                    <option value="4">Rating 4</option>
                    <option value="3">Rating 3</option>
                    <option value="2">Rating 2</option>
                    <option value="1">Rating 1</option>
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
                <div class="col-lg-3 mb-4 vacancy-card" data-profession-id="{{ $data->profession_id }}"
                    data-title="{{ strtolower($data->title) }}" data-employer="{{ strtolower($data->user->name) }}"
                    data-rating="{{ $data->user->average_rating ?? 0 }}">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <a href="{{ route('show-vacancy', $data->id) }}" class="text-decoration-none">
                                <h5 class="card-title font-weight-bold text-dark mb-3" style="line-height: 1.4;">{{ $data->title }}</h5>
                            </a>
                            
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center overflow-hidden" title="{{ $data->user->name }}">
                                    <i class="fas fa-building text-primary mr-2"></i>
                                    <span class="text-dark font-weight-bold text-truncate" style="max-width: 140px;">{{ $data->user->name }}</span>
                                </div>
                                @if($data->user->average_rating > 0)
                                    <div class="d-flex align-items-center box-shadow-sm" style="background-color: #fff8e1; color: #f59e0b; padding: 4px 10px; border-radius: 20px; font-weight: bold; font-size: 0.85rem; border: 1px solid #fde68a;">
                                        <i class="fas fa-star mr-1" style="font-size: 0.8rem;"></i> {{ number_format($data->user->average_rating, 1) }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-calendar-alt text-primary mr-2"></i>
                                <span class="text-muted font-weight-bold" style="font-size: 0.85rem;">
                                    @php
                                        $closingDate = \Carbon\Carbon::parse($data->closing_date);
                                        $startOfDayUser = now()->startOfDay();
                                        $startOfDayClosing = $closingDate->startOfDay();
                                        $diff = $startOfDayUser->diffInDays($startOfDayClosing, false);

                                        if ($diff < 0) {
                                            echo '<span class="text-danger">Lamaran ditutup</span>';
                                        } elseif ($diff == 0) {
                                            echo '<span class="text-warning">Hari ini terakhir</span>';
                                        } else {
                                            echo $diff . ' hari lagi';
                                        }
                                    @endphp
                                </span>
                            </div>
                            <p class="card-text text-muted" style="font-size: 0.9rem;">{{ \Illuminate\Support\Str::limit(strip_tags(str_replace(['<br>', '</p>', '</div>'], ' ', $data->description)), 80, '...') }}</p>
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
            const filterMinRating = document.getElementById('filterMinRating');
            const searchInput = document.getElementById('searchInput');
            const vacancyList = document.getElementById('vacancyList');
            const vacancyCards = document.querySelectorAll('.vacancy-card');

            function applyFilters() {
                const selectedProfessionId = filterProfession.value;
                const searchValue = searchInput.value.trim().toLowerCase();
                const minRating = parseFloat(filterMinRating.value) || 0;

                vacancyList.innerHTML = '';
                let hasVisibleCards = false;

                vacancyCards.forEach(card => {
                    const cardProfessionId = card.getAttribute('data-profession-id');
                    const cardTitle = card.getAttribute('data-title');
                    const cardEmployer = card.getAttribute('data-employer');
                    const cardRating = parseFloat(card.getAttribute('data-rating')) || 0;

                    const matchesProfession = !selectedProfessionId || String(cardProfessionId) === String(
                        selectedProfessionId);
                    const matchesSearch = !searchValue || cardTitle.includes(searchValue) || cardEmployer
                        .includes(searchValue);
                    const matchesRating = minRating === 0 ? true : Math.floor(cardRating) === minRating;

                    if (matchesProfession && matchesSearch && matchesRating) {
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
            filterMinRating.addEventListener('change', applyFilters);
            searchInput.addEventListener('input', applyFilters);
        });
    </script>
@endpush
