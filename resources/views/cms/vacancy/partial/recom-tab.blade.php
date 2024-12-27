<div class="row">
    @if ($recoms->isEmpty())
        <div class="card-body text-center">
            <span class="text-muted">Belum ada rekomendasi</span>
        </div>
    @else
        @foreach ($recoms as $d)
            <div class="col-lg-3 mb-3 mb-lg-0">
                <div class="card shadow-sm">
                    <!-- Photo -->
                    @if (isset($d->servant->servantDetails) && $d->servant->servantDetails->photo)
                        <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $d->servant->servantDetails->photo]) }}"
                            class="card-img-top img-fluid" style="max-height: 150px;"
                            alt="Pembantu {{ $d->servant->name }}">
                    @else
                        <img src="{{ asset('assets/img/undraw_rocket.svg') }}"
                            class="card-img-top img-fluid p-3" style="max-height: 150px;"
                            alt="Pembantu {{ $d->servant->name }}">
                    @endif

                    <!-- Card Content -->
                    <div class="card-body">
                        <ul class="list-unstyled mb-3">
                            <li class="mb-2">
                                <i class="fas fa-user"></i>
                                <strong>Nama:</strong> {{ $d->servant->name }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-calendar-alt"></i>
                                <strong>Usia:</strong>
                                @if (optional($d->servant->servantDetails)->date_of_birth)
                                    {{ \Carbon\Carbon::parse($d->servant->servantDetails->date_of_birth)->age }}
                                    Tahun
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-praying-hands"></i>
                                <strong>Agama:</strong>
                                @if (optional($d->servant->servantDetails)->religion)
                                    {{ $d->servant->servantDetails->religion }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-user-tie"></i>
                                <strong>Profesi:</strong>
                                @if (optional($d->servant->servantDetails)->profession && optional($d->servant->servantDetails->profession)->name)
                                    {{ $d->servant->servantDetails->profession->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </li>
                            <li>
                                <i class="fas fa-briefcase"></i>
                                <strong>Pengalaman:</strong>
                                @if (optional($d->servant->servantDetails)->experience)
                                    {{ $d->servant->servantDetails->experience }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </li>
                        </ul>
                        <p class="card-text text-muted">
                            {{ \Illuminate\Support\Str::limit(optional($d->servant->servantDetails)->description ?? 'Belum ada deskripsi', 100, '...') }}
                        </p>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer text-right">
                        @php
                            $applicationExists = \App\Models\Application::where(
                                'servant_id',
                                $d->servant->id,
                            )
                                ->where('status', 'interview')
                                ->exists();
                        @endphp

                        @if (!$applicationExists)
                            <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                data-target="#recomModal-{{ $d->id }}">
                                <i class="fas fa-check"></i>
                            </a>
                            @include('cms.vacancy.modal.status.recom', ['data' => $d])
                        @endif

                        <a class="btn btn-sm btn-info" href="#" data-toggle="modal"
                            data-target="#servantDetailsModal-{{ $d->id }}">
                            <i class="fas fa-eye"></i>
                        </a>
                        @include('cms.vacancy.modal.servant-detail', ['data' => $d])
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>