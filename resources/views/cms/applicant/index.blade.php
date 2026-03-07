@extends('cms.layouts.main', ['title' => 'Pelamar'])

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pelamar</h1>
        
        <!-- Filter Dropdown -->
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-filter mr-1"></i>
                @if($type == 'hire')
                    Hire
                @elseif($type == 'mandiri')
                    Mandiri
                @else
                    Semua
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="filterDropdown">
                <a class="dropdown-item {{ $type == 'all' ? 'active' : '' }}" href="{{ route('applicant.index', ['type' => 'all']) }}">
                    <i class="fas fa-list mr-2"></i>Semua
                </a>
                <a class="dropdown-item {{ $type == 'hire' ? 'active' : '' }}" href="{{ route('applicant.index', ['type' => 'hire']) }}">
                    <i class="fas fa-handshake mr-2"></i>Hire
                </a>
                <a class="dropdown-item {{ $type == 'mandiri' ? 'active' : '' }}" href="{{ route('applicant.index', ['type' => 'mandiri']) }}">
                    <i class="fas fa-user mr-2"></i>Mandiri
                </a>
            </div>
        </div>
    </div>

    @if($type == 'all' || $type == 'hire')
        @if($hireData->isNotEmpty())
            <!-- Section Hire -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-handshake mr-2"></i>Pelamar - Hire
                    </h6>
                    <span class="badge badge-primary">{{ $hireData->count() }} Pelamar</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($hireData as $d)
                            <div class="col-lg-3 mb-4">
                                <div class="card shadow-sm h-100">
                                    <!-- Photo -->
                                    @if (isset($d->servant->servantDetails) && $d->servant->servantDetails->photo)
                                        <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $d->servant->servantDetails->photo]) }}"
                                            class="card-img-top img-fluid" alt="Pembantu {{ $d->servant->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('assets/img/undraw_rocket.svg') }}" class="card-img-top img-fluid p-3"
                                            alt="Pembantu {{ $d->servant->name }}" style="height: 200px; object-fit: contain;">
                                    @endif

                                    <!-- Card Content -->
                                    <div class="card-body">
                                        <span class="badge badge-info mb-2">Hire</span>
                                        <ul class="list-unstyled mb-3">
                                            @hasrole('superadmin|admin')
                                                <li class="mb-1"><strong>Dihire oleh:</strong> {{ $d->employe->name }}</li>
                                                @if ($d->status == 'schedule')
                                                    <li class="mb-1"><strong>Tanggal Interview:</strong>
                                                        {{ \Carbon\Carbon::parse($d->interview_date)->format('d-m-Y') }}</li>
                                                    <li><strong>Catatan:</strong> {!! $d->notes_interview !!}</li>
                                                    <li class="mb-1"><strong>No Majikan:</strong> {{ $d->employe->employeDetails->phone }}
                                                    </li>
                                                    <li class="mb-1"><strong>No Pembantu:</strong>
                                                        {{ $d->servant->servantDetails->phone }}</li>
                                                    <li class="mb-1"><strong>No Darurat Pembantu:</strong>
                                                        {{ $d->servant->servantDetails->emergency_number }}</li>
                                                @endif
                                            @endhasrole
                                            @if ($d->status == 'interview')
                                                <li class="mb-1"><strong>Link Interview:</strong> <a href="{{ $d->link_interview }}"
                                                        target="_blank" rel="noopener noreferrer">{{ $d->link_interview }}</a></li>
                                                <li class="mb-1"><strong>Tanggal Interview:</strong>
                                                    {{ \Carbon\Carbon::parse($d->interview_date)->format('d-m-Y') }}</li>
                                                <li class="mb-1"><strong>Catatan:</strong> {!! $d->notes_interview !!}</li>
                                            @endif
                                            @if ($d->salary != null)
                                                <li><strong>Gaji:</strong> Rp. {{ number_format($d->salary, 0, ',', '.') }}</li>
                                            @endif
                                        </ul>

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
                                            <li class="mb-2">
                                                <i class="fas fa-briefcase"></i>
                                                <strong>Pengalaman:</strong>
                                                @if (optional($d->servant->servantDetails)->experience)
                                                    {{ $d->servant->servantDetails->experience }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-cogs"></i>
                                                <strong>Inval:</strong>
                                                @if ($d->servant->servantDetails->is_inval)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </li>
                                            <li>
                                                <i class="fas fa-home"></i>
                                                <strong>Pulang Pergi:</strong>
                                                @if ($d->servant->servantDetails->is_stay)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </li>
                                        </ul>
                                        <p class="card-text text-muted">
                                            {{ \Illuminate\Support\Str::limit(optional($d->servant->servantDetails)->description ?? 'Belum ada deskripsi', 100, '...') }}
                                        </p>
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="card-footer">
                                        @include('cms.applicant.partial.hire-footer', ['d' => $d])
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif($type == 'hire')
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Belum ada pelamar Hire</p>
                </div>
            </div>
        @endif
    @endif

    @if($type == 'all' || $type == 'mandiri')
        @if($indieData->isNotEmpty())
            <!-- Section Mandiri -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-user mr-2"></i>Pelamar - Mandiri
                    </h6>
                    <span class="badge badge-success">{{ $indieData->count() }} Pelamar</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($indieData as $d)
                            <div class="col-lg-3 mb-4">
                                <div class="card shadow-sm h-100">
                                    <!-- Photo -->
                                    @if (isset($d->servant->servantDetails) && $d->servant->servantDetails->photo)
                                        <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $d->servant->servantDetails->photo]) }}"
                                            class="card-img-top img-fluid" alt="Pembantu {{ $d->servant->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('assets/img/undraw_rocket.svg') }}" class="card-img-top img-fluid p-3"
                                            alt="Pembantu {{ $d->servant->name }}" style="height: 200px; object-fit: contain;">
                                    @endif

                                    <!-- Card Content -->
                                    <div class="card-body">
                                        <span class="badge badge-success mb-2">Mandiri</span>
                                        <ul class="list-unstyled mb-3">
                                            <li class="mb-1"><strong>Lowongan Pekerjaan:</strong> {{ $d->vacancy->title }}</li>
                                            @hasrole('superadmin|admin')
                                                @if ($d->status == 'schedule')
                                                    <li class="mb-1"><strong>Tanggal Interview:</strong>
                                                        {{ \Carbon\Carbon::parse($d->interview_date)->format('d-m-Y') }}</li>
                                                    <li><strong>Catatan:</strong> {!! $d->notes_interview !!}</li>
                                                    <li class="mb-1"><strong>No Majikan:</strong>
                                                        {{ $d->vacancy->user->employeDetails->phone }}</li>
                                                    <li class="mb-1"><strong>No Pembantu:</strong>
                                                        {{ $d->servant->servantDetails->phone }}</li>
                                                    <li class="mb-1"><strong>No Darurat Pembantu:</strong>
                                                        {{ $d->servant->servantDetails->emergency_number }}</li>
                                                @endif
                                            @endhasrole
                                            @if ($d->status == 'interview')
                                                <li class="mb-1"><strong>Link Interview:</strong> <a href="{{ $d->link_interview }}"
                                                        target="_blank" rel="noopener noreferrer">{{ $d->link_interview }}</a></li>
                                                <li class="mb-1"><strong>Tanggal Interview:</strong>
                                                    {{ \Carbon\Carbon::parse($d->interview_date)->format('d-m-Y') }}</li>
                                                <li class="mb-1"><strong>Catatan:</strong> {!! $d->notes_interview !!}</li>
                                            @endif
                                            @if ($d->salary != null)
                                                <li><strong>Gaji:</strong> Rp. {{ number_format($d->salary, 0, ',', '.') }}</li>
                                            @endif
                                        </ul>

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
                                            <li class="mb-2">
                                                <i class="fas fa-briefcase"></i>
                                                <strong>Pengalaman:</strong>
                                                @if (optional($d->servant->servantDetails)->experience)
                                                    {{ $d->servant->servantDetails->experience }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-cogs"></i>
                                                <strong>Inval:</strong>
                                                @if ($d->servant->servantDetails->is_inval)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </li>
                                            <li>
                                                <i class="fas fa-home"></i>
                                                <strong>Pulang Pergi:</strong>
                                                @if ($d->servant->servantDetails->is_stay)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </li>
                                        </ul>
                                        <p class="card-text text-muted">
                                            {{ \Illuminate\Support\Str::limit(optional($d->servant->servantDetails)->description ?? 'Belum ada deskripsi', 100, '...') }}
                                        </p>
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="card-footer text-right">
                                        @include('cms.applicant.partial.indie-footer', ['d' => $d])
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif($type == 'mandiri')
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Belum ada pelamar Mandiri</p>
                </div>
            </div>
        @endif
    @endif

    @if($hireData->isEmpty() && $indieData->isEmpty() && $type == 'all')
        <div class="card shadow mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted">Belum ada pelamar</p>
            </div>
        </div>
    @endif
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/summernote/summernote-bs4.min.css') }}">
@endpush

@push('custom-script')
    <script src="{{ asset('assets/vendor/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Global Event Delegation for Salary Modals
            
            // 1. Handle "Jenis Penggajian" Radio Change
            $(document).on('change', '.salary-type-radio', function() {
                var $this = $(this);
                var $modal = $this.closest('.modal'); // Find the specific modal instance
                var targetId = $this.data('target');

                // Hide all salary-sections IN THIS MODAL only
                $modal.find('.salary-section').hide();
                
                // Show the target section
                if(targetId) {
                    $(targetId).show();
                }

                // If "Fee" is selected, trigger the infal switch to set correct state
                if ($this.val() === 'fee') {
                    // Find the switch in this modal and trigger change
                    $modal.find('.is-infal-switch').trigger('change');
                }

                // Recalculate Scheme
                var $schemeSelect = $modal.find('.scheme-select');
                if ($schemeSelect.length > 0 && $schemeSelect.val()) {
                    showSchemeDetail($schemeSelect[0]);
                }
            });

            // 2. Handle "Mode Infal" Switch Change
            $(document).on('change', '.is-infal-switch', function() {
                var $this = $(this);
                var $modal = $this.closest('.modal');
                var isInfal = $this.is(':checked');
                var targetInfal = $this.data('target');
                var targetRegular = $this.data('regular');

                if (isInfal) {
                    $(targetInfal).show();
                    $(targetRegular).hide();
                } else {
                    $(targetInfal).hide();
                    $(targetRegular).show();
                }

                // Recalculate Scheme
                var $schemeSelect = $modal.find('.scheme-select');
                if ($schemeSelect.length > 0 && $schemeSelect.val()) {
                    showSchemeDetail($schemeSelect[0]);
                }
            });

            // 3. Initialize Correct State when Modal Opens
            $(document).on('shown.bs.modal', '.modal', function () {
                var $modal = $(this);
                // Check if this is a salary modal (has salary-type-radio)
                var $radios = $modal.find('.salary-type-radio');
                if ($radios.length > 0) {
                     var $checked = $modal.find('.salary-type-radio:checked');
                     if ($checked.length > 0) {
                         $checked.trigger('change');
                     } else {
                         // Clean state if nothing checked
                         $modal.find('.salary-section').hide();
                     }
                }
            });

            // ======================================
            // 4. Rupiah Input Formatting (titik ribuan)
            // ======================================

            function formatRupiahInput(angka) {
                var numberStr = angka.toString().replace(/[^0-9]/g, '');
                if (numberStr === '') return '';
                return numberStr.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function parseRupiahInput(formatted) {
                return parseInt(formatted.replace(/\./g, '')) || 0;
            }

            // Event: Format input saat user mengetik
            $(document).on('input', '.rupiah-input', function() {
                var $this = $(this);
                var cursorPos = this.selectionStart;
                var oldVal = $this.val();
                var oldLen = oldVal.length;

                // Ambil angka mentah, format ulang
                var rawNumber = parseRupiahInput(oldVal);
                var formatted = rawNumber > 0 ? formatRupiahInput(rawNumber) : '';
                
                $this.val(formatted);

                // Update hidden input
                var targetName = $this.data('target');
                if (targetName) {
                    $this.siblings('input[name="' + targetName + '"]').val(rawNumber > 0 ? rawNumber : '');
                }

                // Adjust cursor position
                var newLen = formatted.length;
                var newPos = cursorPos + (newLen - oldLen);
                this.setSelectionRange(newPos, newPos);

                // Recalculate Scheme jika ada
                var $modal = $this.closest('.modal');
                var $schemeSelect = $modal.find('.scheme-select');
                if ($schemeSelect.length > 0 && $schemeSelect.val()) {
                    showSchemeDetail($schemeSelect[0]);
                }
            });

            // ======================================
            // 5. BPJS Real-time Salary Calculator
            // ======================================

            // Format angka ke format Indonesia (titik sebagai pemisah ribuan)
            function formatRupiah(angka) {
                var number = Math.round(angka);
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Ambil nominal gaji aktif dari modal (dari hidden input)
            function getActiveSalary($modal) {
                var salary = 0;
                var salaryType = $modal.find('.salary-type-radio:checked').val();

                if (salaryType === 'contract') {
                    salary = parseFloat($modal.find('input[name="contract_salary"]').val()) || 0;
                } else if (salaryType === 'fee') {
                    var isInfal = $modal.find('.is-infal-switch').is(':checked');
                    if (isInfal) {
                        salary = parseFloat($modal.find('input[name="fee_salary_infal"]').val()) || 0;
                    } else {
                        salary = parseFloat($modal.find('input[name="fee_salary_regular"]').val()) || 0;
                    }
                }

                return salary;
            }



            // ======================================
            // 6. Scheme Detail on Selection
            // ======================================
            // Initialize scheme detail on modal open
            $(document).on('shown.bs.modal', '.modal', function () {
                var $schemeSelect = $(this).find('.scheme-select');
                if ($schemeSelect.length > 0 && $schemeSelect.val()) {
                    showSchemeDetail($schemeSelect[0]);
                }
            });
        });

        // Global function for scheme detail display
        function showSchemeDetail(selectEl) {
            var $select = $(selectEl);
            var modalId = $select.data('modal-id');
            var $summary = $('#schemeSummary-' + modalId);
            var $option = $select.find(':selected');
            var $modal = $select.closest('.modal');

            if (!$select.val()) {
                $summary.slideUp(200);
                return;
            }

            function fmtVal(item) {
                if (item.unit === 'Rp') {
                    var num = parseFloat(item.value) || 0;
                    return 'Rp ' + num.toLocaleString('id-ID');
                }
                return item.value + '%';
            }

            // Format angka ke format Indonesia (titik sebagai pemisah ribuan)
            function formatRupiah(angka) {
                var number = Math.round(angka);
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Ambil nominal gaji aktif dari modal
            function getActiveSalary($modal) {
                var salary = 0;
                var salaryType = $modal.find('.salary-type-radio:checked').val();

                if (salaryType === 'contract') {
                    salary = parseFloat($modal.find('input[name="contract_salary"]').val()) || 0;
                } else if (salaryType === 'fee') {
                    var isInfal = $modal.find('.is-infal-switch').is(':checked');
                    if (isInfal) {
                        salary = parseFloat($modal.find('input[name="fee_salary_infal"]').val()) || 0;
                    } else {
                        salary = parseFloat($modal.find('input[name="fee_salary_regular"]').val()) || 0;
                    }
                }

                return salary;
            }

            try {
                var clientData = JSON.parse($option.attr('data-client')) || [];
                var mitraData = JSON.parse($option.attr('data-mitra')) || [];

                var clientHtml = clientData.map(function(f) {
                    return '<span class="badge badge-light border mr-1">' + f.label + ': <strong>' + fmtVal(f) + '</strong></span>';
                }).join(' ');

                var mitraHtml = mitraData.map(function(f) {
                    return '<span class="badge badge-light border mr-1">' + f.label + ': <strong>' + fmtVal(f) + '</strong></span>';
                }).join(' ');

                $summary.find('.scheme-client-detail').html(clientHtml);
                $summary.find('.scheme-mitra-detail').html(mitraHtml);

                // Hitung Total Tagihan Majikan & Estimasi Gaji Bersih Pekerja
                var gajiPokok = getActiveSalary($modal);
                
                // Hitung total biaya klien (ditanggung majikan)
                var totalClientExtra = 0;
                clientData.forEach(function(item) {
                    var val = parseFloat(item.value) || 0;
                    if (item.unit === '%') {
                        totalClientExtra += (gajiPokok * val / 100);
                    } else {
                        totalClientExtra += val;
                    }
                });

                // Hitung total potongan mitra (dipotong dari gaji pekerja)
                var totalMitraDeduction = 0;
                mitraData.forEach(function(item) {
                    var val = parseFloat(item.value) || 0;
                    if (item.unit === '%') {
                        totalMitraDeduction += (gajiPokok * val / 100);
                    } else {
                        totalMitraDeduction += val;
                    }
                });

                var totalTagihanMajikan = gajiPokok + totalClientExtra;
                var gajiBersihPekerja = gajiPokok - totalMitraDeduction;

                $summary.find('.scheme-employer-total').text(formatRupiah(totalTagihanMajikan));
                $summary.find('.scheme-worker-net').text(formatRupiah(Math.max(0, gajiBersihPekerja)));

                $summary.slideDown(200);
            } catch(e) {
                $summary.slideUp(200);
            }
        }
    </script>
@endpush
