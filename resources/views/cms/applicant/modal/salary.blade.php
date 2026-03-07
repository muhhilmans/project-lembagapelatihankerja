{{-- Modal Gaji - Salary Information --}}
<div class="modal fade" id="salaryModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="salaryModalLabel-{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="salaryModalLabel-{{ $data->id }}">
                    <i class="fas fa-money-bill-wave mr-2"></i>Pengaturan Gaji & Kontrak
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                @if (isset($data->servant->servantDetails) && $data->servant->servantDetails->photo)
                                    <img src="{{ route('getImage', ['path' => 'photo', 'imageName' => $data->servant->servantDetails->photo]) }}"
                                        class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-gray-200 d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="fas fa-user text-gray-500"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0">{{ $data->servant->name ?? 'Pembantu' }}</h6>
                                <small class="text-muted">{{ $data->vacancy->title ?? 'Lowongan' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('applicant.salary.update', $data->id) }}" method="POST" id="salaryForm-{{ $data->id }}">
                    @csrf
                    @method('PUT')

                    {{-- Jenis Penggajian --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Jenis Penggajian <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="salaryTypeContract-{{ $data->id }}" name="salary_type"
                                        class="custom-control-input salary-type-radio" value="contract"
                                        {{ $data->salary_type == 'contract' ? 'checked' : '' }} required
                                        data-target="#contractSection-{{ $data->id }}">
                                    <label class="custom-control-label" for="salaryTypeContract-{{ $data->id }}">
                                        <i class="fas fa-file-contract mr-1"></i> Kontrak (Agensi)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="salaryTypeFee-{{ $data->id }}" name="salary_type"
                                        class="custom-control-input salary-type-radio" value="fee"
                                        {{ $data->salary_type == 'fee' ? 'checked' : '' }} required
                                        data-target="#feeSection-{{ $data->id }}">
                                    <label class="custom-control-label" for="salaryTypeFee-{{ $data->id }}">
                                        <i class="fas fa-hand-holding-usd mr-1"></i> Fee (Langsung/Infal)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Section Kontrak --}}
                    <div id="contractSection-{{ $data->id }}" class="salary-section" style="display: none;">
                        <h6 class="text-primary font-weight-bold mb-3">Detail Kontrak Agensi</h6>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Gaji Bulanan (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control rupiah-input" data-target="contract_salary" placeholder="0" inputmode="numeric" value="{{ $data->salary_type == 'contract' && $data->salary ? number_format($data->salary, 0, ',', '.') : '' }}">
                                    <input type="hidden" name="contract_salary" value="{{ $data->salary_type == 'contract' ? $data->salary : '' }}">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Biaya Administrasi (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control rupiah-input" data-target="admin_fee" placeholder="0" inputmode="numeric" value="{{ $data->admin_fee ? number_format($data->admin_fee, 0, ',', '.') : '' }}">
                                    <input type="hidden" name="admin_fee" value="{{ $data->admin_fee }}">
                                </div>
                                <small class="form-text text-muted">Ditransfer majikan ke Sipembantu.</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Tanggal Mulai (Start Date) <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="contract_start_date" value="{{ $data->work_start_date }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Tanggal Selesai (End Date) <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="contract_end_date" value="{{ $data->work_end_date }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Durasi Garansi <span class="text-danger">*</span></label>
                                <select class="form-control" name="garansi_id" id="garansiSelect-{{ $data->id }}" onchange="setGaransiPrice('{{ $data->id }}')">
                                    <option value="">-- Pilih Garansi --</option>
                                    @if(isset($garansiOptions))
                                        @foreach($garansiOptions as $garansi)
                                            <option value="{{ $garansi->id }}" data-price="{{ $garansi->price }}" {{ $data->garansi_id == $garansi->id ? 'selected' : '' }}>
                                                {{ $garansi->name }} (Maks {{ $garansi->max_replacements }} Tukar)
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <!-- Field fallback for old data if no garansi_id -->
                                @if(!$data->garansi_id && $data->warranty_duration)
                                    <small class="text-muted d-block mt-1">Lama: {{ $data->warranty_duration }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="form-row" id="garansiInfoContainer-{{ $data->id }}" style="display: {{ $data->garansi_id && $data->garansi ? 'block' : 'none' }}; width: 100%;">
                            <div class="col-md-12">
                                <div class="alert alert-info py-2 mb-3">
                                    <i class="fas fa-info-circle mr-1"></i> 
                                    <span id="garansiInfoText-{{ $data->id }}">
                                        @if($data->garansi)
                                            <strong>{{ $data->garansi->name }} (Maks. {{ $data->garansi->max_replacements }}x Tukar)</strong> - Harga: Rp {{ number_format($data->garansi->price, 0, ',', '.') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="alert alert-info py-2" id="contractDurationInfo-{{ $data->id }}" style="display: none;">
                                    <i class="fas fa-info-circle mr-1"></i> Durasi Pekerjaan: <strong id="contractMonths-{{ $data->id }}">0</strong> Bulan. 
                                    (Majikan perlu membayarkan gaji untuk <strong id="contractMonthsText-{{ $data->id }}">0</strong> bulan ke pekerja).
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section Fee --}}
                    <div id="feeSection-{{ $data->id }}" class="salary-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-success font-weight-bold mb-0">Detail Fee / Infal</h6>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input is-infal-switch" id="isInfalSwitch-{{ $data->id }}" 
                                    name="is_infal" value="1" {{ $data->is_infal ? 'checked' : '' }}
                                    data-target="#infalOptions-{{ $data->id }}"
                                    data-regular="#regularOptions-{{ $data->id }}">
                                <label class="custom-control-label font-weight-bold" for="isInfalSwitch-{{ $data->id }}">Mode Infal</label>
                            </div>
                        </div>

                        {{-- Regular Options --}}
                        <div id="regularOptions-{{ $data->id }}" class="fee-subsection">
                            <div class="alert alert-info py-2"><i class="fas fa-info-circle mr-1"></i> Mode Reguler (Bayar Bulanan)</div>
                             <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Gaji Bulanan (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        {{-- Gunakan name salary_regular agar tidak bentrok --}}
                                        <input type="text" class="form-control rupiah-input" data-target="fee_salary_regular" placeholder="0" inputmode="numeric" value="{{ $data->salary_type == 'fee' && !$data->is_infal && $data->salary ? number_format($data->salary, 0, ',', '.') : '' }}">
                                        <input type="hidden" name="fee_salary_regular" value="{{ $data->salary_type == 'fee' && !$data->is_infal ? $data->salary : '' }}"> 
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tanggal Pembayaran Majikan</label>
                                    <input type="date" class="form-control" name="fee_end_date_regular" value="{{ $data->work_end_date }}">
                                    <small class="form-text text-muted">End date pembantu otomatis H+7 dari tanggal pembayaran ini.</small>
                                </div>
                            </div>
                        </div>

                        {{-- Infal Options --}}
                        <div id="infalOptions-{{ $data->id }}" class="fee-subsection" style="display: none;">
                             <div class="alert alert-warning py-2"><i class="fas fa-bolt mr-1"></i> Mode Infal (Sementara)</div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Frekuensi Bayar <span class="text-danger">*</span></label>
                                    <select class="form-control infal-frequency-select" name="infal_frequency" 
                                        data-id="{{ $data->id }}" id="infalFrequency-{{ $data->id }}">
                                        <option value="monthly" {{ $data->infal_frequency == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                        <option value="hourly" {{ $data->infal_frequency == 'hourly' ? 'selected' : '' }}>Per Jam</option>
                                        <option value="daily" {{ $data->infal_frequency == 'daily' ? 'selected' : '' }}>Harian</option>
                                        <option value="weekly" {{ $data->infal_frequency == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-8">
                                    <label>Nominal Gaji (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" class="form-control rupiah-input" data-target="fee_salary_infal" placeholder="0" inputmode="numeric" value="{{ $data->salary_type == 'fee' && $data->is_infal && $data->salary ? number_format($data->salary, 0, ',', '.') : '' }}">
                                        <input type="hidden" name="fee_salary_infal" value="{{ $data->salary_type == 'fee' && $data->is_infal ? $data->salary : '' }}">
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Hourly Options --}}
                            <div id="hourlyOptions-{{ $data->id }}" class="form-row hourly-options" style="display: none;">
                                <div class="form-group col-md-4">
                                    <label>Jam Masuk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control hourly-input time-in-input" name="infal_time_in" 
                                        placeholder="HH:mm"
                                        value="{{ $data->infal_time_in ? \Carbon\Carbon::parse($data->infal_time_in)->format('H:i') : '' }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Jam Pulang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control hourly-input time-out-input" name="infal_time_out" 
                                        placeholder="HH:mm"
                                        value="{{ $data->infal_time_out ? \Carbon\Carbon::parse($data->infal_time_out)->format('H:i') : '' }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Harga Per Jam <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" class="form-control rupiah-input hourly-input hourly-rate-input" 
                                            data-target="infal_hourly_rate" placeholder="0" inputmode="numeric" 
                                            value="{{ $data->infal_hourly_rate ? number_format($data->infal_hourly_rate, 0, ',', '.') : '' }}">
                                        <input type="hidden" name="infal_hourly_rate" class="hourly-rate-val" value="{{ $data->infal_hourly_rate }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label id="startDateLabel-{{ $data->id }}">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="infal_start_date" value="{{ $data->work_start_date }}">
                                </div>
                                <div class="form-group col-md-6" id="endDateGroup-{{ $data->id }}">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="infal_end_date" value="{{ $data->work_end_date }}">
                                </div>
                            </div>
                        </div>

<script>
    try {
        (function() {
            var id = '{{ $data->id }}';
            var container = document.getElementById('salaryModal-' + id);
            if (!container) return; // Prevent crash if Element is not found via DataTables strip

            var select = document.getElementById('infalFrequency-' + id);
            var timeIn = container.querySelector('.time-in-input');
            var timeOut = container.querySelector('.time-out-input');
            var hourlyRate = container.querySelector('.hourly-rate-input');
            var hourlyRateVal = container.querySelector('.hourly-rate-val');
            
            var hourlyOptions = document.getElementById('hourlyOptions-' + id);
            var endDateGroup = document.getElementById('endDateGroup-' + id);
            var startDateLabel = document.getElementById('startDateLabel-' + id);
            
            var feeSalaryInput = container.querySelector('input[data-target="fee_salary_infal"]');
            var feeSalaryHidden = container.querySelector('input[name="fee_salary_infal"]');
            
            var contractStartDate = container.querySelector('input[name="contract_start_date"]');
            var contractEndDate = container.querySelector('input[name="contract_end_date"]');
            var contractDurationInfo = document.getElementById('contractDurationInfo-' + id);
            var contractMonths = document.getElementById('contractMonths-' + id);
            var contractMonthsText = document.getElementById('contractMonthsText-' + id);

            function toggleInfalFrequency() {
                if(!select) return;
                var val = select.value;
                if (val === 'hourly') {
                    if(hourlyOptions) hourlyOptions.style.display = 'flex';
                    if(endDateGroup) endDateGroup.style.display = 'none';
                    if(startDateLabel) startDateLabel.innerHTML = 'Tanggal Kerja <span class="text-danger">*</span>';
                    if(feeSalaryInput) {
                        feeSalaryInput.readOnly = true;
                        feeSalaryInput.className = 'form-control rupiah-input bg-light';
                    }
                    calculateHourlyTotal();
                } else {
                    if(hourlyOptions) hourlyOptions.style.display = 'none';
                    if(endDateGroup) endDateGroup.style.display = 'block';
                    if(startDateLabel) startDateLabel.innerHTML = 'Start Date <span class="text-danger">*</span>';
                    if(feeSalaryInput) {
                        feeSalaryInput.readOnly = false;
                        feeSalaryInput.className = 'form-control rupiah-input';
                    }
                }
            }

            function calculateContractDuration() {
                if (!contractStartDate || !contractEndDate || !contractDurationInfo) return;
                var start = contractStartDate.value;
                var end = contractEndDate.value;

                if (start && end) {
                    var d1 = new Date(start);
                    var d2 = new Date(end);
                    var months = (d2.getFullYear() - d1.getFullYear()) * 12 + (d2.getMonth() - d1.getMonth());
                    if (d2.getDate() < d1.getDate()) months--;
                    if (months > 0) {
                        contractDurationInfo.style.display = 'block';
                        contractMonths.innerText = months;
                        contractMonthsText.innerText = months;
                    } else {
                        contractDurationInfo.style.display = 'none';
                    }
                } else {
                    contractDurationInfo.style.display = 'none';
                }
            }

            function calculateHourlyTotal() {
                if (!timeIn || !timeOut || !hourlyRateVal) return;
                var timeInStr = timeIn.value;
                var timeOutStr = timeOut.value;
                var rateStr = hourlyRateVal.value;

                if (!timeInStr || !timeOutStr || !rateStr) return;

                var date = new Date().toDateString();
                var d1 = new Date(date + ' ' + timeInStr);
                var d2 = new Date(date + ' ' + timeOutStr);
                
                var diffMs = d2 - d1;
                if (diffMs < 0) return;

                var diffHrs = diffMs / (1000 * 60 * 60);
                var rate = parseFloat(rateStr);
                var total = Math.round(diffHrs * rate);

                if (!isNaN(total) && total >= 0) {
                    var formatted = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    if(feeSalaryInput) feeSalaryInput.value = formatted;
                    if(feeSalaryHidden) feeSalaryHidden.value = total;
                }
            }

            if(select) select.addEventListener('change', toggleInfalFrequency);
            if(timeIn) timeIn.addEventListener('change', calculateHourlyTotal);
            if(timeOut) timeOut.addEventListener('change', calculateHourlyTotal);
            if(hourlyRate) {
                hourlyRate.addEventListener('keyup', function() {
                    var val = this.value.replace(/\./g, '').replace(/,/g, '.');
                    if(hourlyRateVal) hourlyRateVal.value = val;
                    calculateHourlyTotal();
                });
                hourlyRate.addEventListener('change', function() {
                    var val = this.value.replace(/\./g, '').replace(/,/g, '.');
                    if(hourlyRateVal) hourlyRateVal.value = val;
                    calculateHourlyTotal();
                });
            }

            // Initialize
            toggleInfalFrequency();
            if(contractStartDate) contractStartDate.addEventListener('change', calculateContractDuration);
            if(contractEndDate) contractEndDate.addEventListener('change', calculateContractDuration);
            calculateContractDuration();
        })();
    } catch(e) {
        console.error("Error init salary modal JS:", e);
    }
</script>

<script>
    function setGaransiPrice(id) {
        var select = document.getElementById('garansiSelect-' + id);
        var infoContainer = document.getElementById('garansiInfoContainer-' + id);
        var infoText = document.getElementById('garansiInfoText-' + id);

        if (select.value) {
            var selectedOption = select.options[select.selectedIndex];
            var price = selectedOption.getAttribute('data-price');
            var nameText = selectedOption.text;
            
            infoContainer.style.display = 'block';
            if (price) {
                var formattedPrice = parseFloat(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                infoText.innerHTML = "<strong>" + nameText.trim() + "</strong> - Harga: Rp " + formattedPrice;
            }
        } else {
            infoContainer.style.display = 'none';
            infoText.innerHTML = '';
        }
    }
</script>

                        <hr>
                        <h6 class="font-weight-bold mb-3">Pengaturan Tambahan</h6>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Potongan Izin (Rp/hari)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control rupiah-input" data-target="deduction_amount" placeholder="0" inputmode="numeric" value="{{ $data->deduction_amount ? number_format($data->deduction_amount, 0, ',', '.') : '' }}">
                                    <input type="hidden" name="deduction_amount" value="{{ $data->deduction_amount }}">
                                </div>
                                <small class="form-text text-muted">Jika tidak masuk dengan izin.</small>
                            </div>

                        </div>

                        {{-- Opsi Fee (Skema Biaya) --}}
                        @if(isset($schemeOptions) && $schemeOptions->count() > 0)
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label><i class="fas fa-money-bill-wave mr-1 text-info"></i>Opsi Fee (Skema Biaya)</label>
                                <select class="form-control scheme-select" name="scheme_id" data-modal-id="{{ $data->id }}" onchange="showSchemeDetail(this)">
                                    <option value="">-- Pilih Skema Biaya --</option>
                                    @foreach($schemeOptions as $scheme)
                                        <option value="{{ $scheme->id }}" 
                                            data-client="{{ json_encode($scheme->client_data) }}"
                                            data-mitra="{{ json_encode($scheme->mitra_data) }}"
                                            {{ $data->scheme_id == $scheme->id ? 'selected' : '' }}>
                                            {{ $scheme->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Pilih skema biaya fee dari client.</small>

                                {{-- Detail Skema yang dipilih --}}
                                <div id="schemeSummary-{{ $data->id }}" class="scheme-summary mt-2" style="display:none;">
                                    <div class="alert alert-light border p-2 mb-0" style="font-size: 0.85rem;">
                                        {{-- Total Tagihan Majikan & Estimasi Gaji Bersih --}}
                                        <div class="mb-1">
                                            <i class="fas fa-file-invoice-dollar text-primary mr-1"></i>
                                            Total Tagihan Majikan: 
                                            <strong class="text-primary">Rp <span class="scheme-employer-total">0</span></strong>
                                        </div>
                                        <div class="mb-2">
                                            <i class="fas fa-wallet text-success mr-1"></i>
                                            Estimasi Gaji Bersih Pekerja: 
                                            <strong class="text-success">Rp <span class="scheme-worker-net">0</span></strong>
                                        </div>
                                        <hr class="my-2">
                                        <div class="mb-1">
                                            <i class="fas fa-user-tie text-primary mr-1"></i>
                                            <strong>Klien:</strong> <span class="scheme-client-detail"></span>
                                        </div>
                                        <div>
                                            <i class="fas fa-user-friends text-success mr-1"></i>
                                            <strong>Mitra:</strong> <span class="scheme-mitra-detail"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning px-4">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


