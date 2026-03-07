@extends('cms.layouts.main', ['title' => 'Rating & Ulasan'])

@section('content')
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="h3 mb-0 text-gray-800">Rating & Ulasan</h1>
    </div>

    <!-- Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Ulasan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('reviews.index') }}" method="GET" class="form-inline">
                <div class="form-group mb-2">
                    <label for="filter" class="mr-2">Tampilkan Akun:</label>
                    <select name="filter" id="filter" class="form-control">
                        <option value="semua" {{ $filter == 'semua' ? 'selected' : '' }}>Semua (Majikan & Pekerja)</option>
                        <option value="majikan" {{ $filter == 'majikan' ? 'selected' : '' }}>Majikan (Sudah Ada Ulasan)</option>
                        <option value="pembantu" {{ $filter == 'pembantu' ? 'selected' : '' }}>Pekerja (Sudah Ada Ulasan)</option>
                        <option value="majikan_null" {{ $filter == 'majikan_null' ? 'selected' : '' }}>Majikan (Belum Ada Ulasan)</option>
                        <option value="pembantu_null" {{ $filter == 'pembantu_null' ? 'selected' : '' }}>Pekerja (Belum Ada Ulasan)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2 ml-3">Terapkan Filter</button>
            </form>
        </div>
    </div>

    <!-- DataTables -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Rating & Ulasan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 30%">Nama Akun</th>
                            <th style="width: 15%">Peran</th>
                            <th style="width: 15%">Rata-rata Rating</th>
                            <th style="width: 15%">Total Ulasan</th>
                            <th style="width: 20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong><br>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $user->hasRole('majikan') ? 'badge-info' : 'badge-secondary' }}">
                                        {{ ucfirst($user->roles->first()?->name ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $avg = $user->average_rating;
                                    @endphp
                                    @if ($user->review_count > 0)
                                        <i class="fas fa-star text-warning"></i>
                                        <strong>{{ number_format($avg, 1) }}</strong> / 5.0
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light p-2 border">
                                        {{ $user->review_count }} Ulasan
                                    </span>
                                </td>
                                <td>
                                    @if ($user->review_count > 0)
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#reviewModal{{ $user->id }}">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="fas fa-eye-slash"></i> Belum Ada Ulasan
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals for details -->
    @foreach ($users as $user)
        @if ($user->review_count > 0)
            <div class="modal fade" id="reviewModal{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                Ulasan untuk: <strong>{{ $user->name }}</strong>
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach ($user->receivedReviews->sortByDesc('created_at') as $review)
                                    <li class="list-group-item p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <h6 class="mb-0 font-weight-bold">
                                                    {{ $review->reviewer->name ?? 'User Terhapus' }}
                                                    <span class="badge badge-secondary ml-1" style="font-size: 0.7rem;">
                                                        {{ $review->reviewer ? ucfirst($review->reviewer->roles->first()?->name ?? 'N/A') : '' }}
                                                    </span>
                                                </h6>
                                                <small class="text-muted">{{ $review->created_at->format('d M Y, H:i') }}</small>
                                            </div>
                                            <div class="text-right">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $review->rating)
                                                        <i class="fas fa-star text-warning" style="font-size: 0.9rem;"></i>
                                                    @else
                                                        <i class="far fa-star text-warning" style="font-size: 0.9rem;"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="mb-0 text-dark" style="font-size: 0.95rem;">
                                            "{{ $review->comment }}"
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
