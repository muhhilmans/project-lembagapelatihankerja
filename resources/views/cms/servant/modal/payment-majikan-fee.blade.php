<div class="modal fade" id="paymentMajikanFeeModal-{{ $index }}" tabindex="-1" role="dialog"
    aria-labelledby="paymentMajikanFeeLabel-{{ $index }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="paymentMajikanFeeLabel-{{ $index }}">Upload Bukti
                    Pembayaran (Fee - Bulan {{ \Carbon\Carbon::parse($month)->format('F Y') }})</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('payment-majikan-fee.upload', $data->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="month" value="{{ $month }}">

                    @if($needQuantity ?? false)
                        {{-- Input Jumlah untuk hourly/daily/weekly --}}
                        <div class="form-group">
                            <label for="quantity_fee_{{ $index }}" class="font-weight-bold">Jumlah {{ $satuanLabel ?? 'Hari' }} Kerja <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quantity" id="quantity_fee_{{ $index }}" 
                                min="1" required placeholder="Masukkan jumlah {{ strtolower($satuanLabel ?? 'hari') }}"
                                value="{{ $defaultQuantity ?? '' }}"
                                onchange="hitungTotalFee_{{ $index }}()" oninput="hitungTotalFee_{{ $index }}()">
                            <small class="form-text text-muted">
                                Tarif: Rp. {{ number_format($tarifSatuan ?? $data->salary, 0, ',', '.') }} / {{ $satuanLabel ?? 'Hari' }}
                            </small>
                        </div>
                        <div class="alert alert-success" id="previewTotalFee_{{ $index }}" style="display:none;">
                            <i class="fas fa-calculator mr-1"></i>
                            <strong>Rincian Tagihan:</strong><br>
                            <span id="rincianFee_{{ $index }}"></span>
                            <hr class="my-2">
                            <strong>Total yang harus dibayar: <span id="totalBayarFee_{{ $index }}">Rp. 0</span></strong>
                        </div>

                    @endif

                    @if($data->salary_type == 'fee')
                            <div class="form-group d-none">
                                <label for="absence_days_{{ $index }}" class="font-weight-bold">Jumlah Hari Tidak Masuk (Izin)</label>
                                <input type="number" class="form-control" name="absence_days" id="absence_days_{{ $index }}" 
                                    min="0" placeholder="0" value="0"
                                    onchange="hitungPotonganFee_{{ $index }}()" oninput="hitungPotonganFee_{{ $index }}()">
                                <input type="hidden" name="absence_reason" id="absence_reason_{{ $index }}" value="">
                                <input type="hidden" name="extra_deduction" id="extra_deduction_{{ $index }}" value="">
                                <small class="form-text text-muted">
                                    Potongan: Rp. {{ number_format($data->deduction_amount, 0, ',', '.') }} / Hari
                                </small>
                            </div>
                            <div class="alert alert-warning" id="previewPotonganFee_{{ $index }}" style="display:none;">
                                <i class="fas fa-minus-circle mr-1"></i>
                                <strong>Total Potongan Izin: <span id="totalPotonganFee_{{ $index }}">Rp. 0</span></strong><br>
                                <small>Potongan otomatis dihitung ke total tagihan Anda.</small>
                            </div>
                            
                            <script>
                                function hitungTagihanUnifikasi_{{ $index }}() {
                                    var qtyInput = document.getElementById('quantity_fee_{{ $index }}');
                                    var qty = qtyInput ? (parseInt(qtyInput.value) || 0) : 1;
                                    var tarif = {{ $tarifSatuan ?? $data->salary }};
                                    var gajiPokok = tarif * qty;
                                    
                                    var absence = 0;
                                    var extraDeduction = 0;
                                    var absenceInput = document.getElementById('absence_days_{{ $index }}');
                                    if(absenceInput) { absence = parseInt(absenceInput.value) || 0; }
                                    var extraInput = document.getElementById('extra_deduction_{{ $index }}');
                                    if(extraInput) { extraDeduction = parseInt(extraInput.value) || 0; }
                                    
                                    var deductionPerDay = {{ $data->deduction_amount ?? 0 }};
                                    var totalDeduction = (absence * deductionPerDay) + extraDeduction;
                                    
                                    var newBaseSalary = Math.max(0, gajiPokok - totalDeduction);
                                    
                                    var clientData = @json($data->scheme && is_array($data->scheme->client_data) ? $data->scheme->client_data : []);
                                    var clientFees = 0;
                                    var rincianHtml = '';
                                    
                                    if (qtyInput) {
                                        rincianHtml += 'Gaji Pokok: Rp. ' + tarif.toLocaleString('id-ID') + ' × ' + Math.max(0, qty) + ' {{ $satuanLabel ?? "Hari" }} = <strong>Rp. ' + gajiPokok.toLocaleString('id-ID') + '</strong><br>';
                                    }
                                    
                                    for (var i = 0; i < clientData.length; i++) {
                                        var fee = clientData[i];
                                        var amount = 0;
                                        if (fee.unit === '%') {
                                            amount = newBaseSalary * (parseFloat(fee.value) / 100);
                                            rincianHtml += (fee.name || 'Fee') + ': ' + fee.value + '% = Rp. ' + Math.ceil(amount).toLocaleString('id-ID') + '<br>';
                                        } else {
                                            amount = parseFloat(fee.value);
                                            rincianHtml += (fee.name || 'Fee') + ': Rp. ' + Math.ceil(amount).toLocaleString('id-ID') + '<br>';
                                        }
                                        clientFees += amount;
                                    }
                                    
                                    var newTotal = Math.ceil(newBaseSalary + clientFees);
                                    
                                    var previewPotDiv = document.getElementById('previewPotonganFee_{{ $index }}');
                                    var totalPotSpan = document.getElementById('totalPotonganFee_{{ $index }}');
                                    if (previewPotDiv && totalPotSpan) {
                                        if (absence > 0 || extraDeduction > 0) {
                                            previewPotDiv.style.display = 'block';
                                            totalPotSpan.textContent = '- Rp. ' + totalDeduction.toLocaleString('id-ID');
                                        } else {
                                            previewPotDiv.style.display = 'none';
                                        }
                                    }
                                    
                                    var previewDiv = document.getElementById('previewTotalFee_{{ $index }}');
                                    var rincianSpan = document.getElementById('rincianFee_{{ $index }}');
                                    var totalSpan = document.getElementById('totalBayarFee_{{ $index }}');
                                    if (previewDiv && rincianSpan && totalSpan) {
                                        if (qty > 0) {
                                            previewDiv.style.display = 'block';
                                            rincianSpan.innerHTML = rincianHtml;
                                            totalSpan.textContent = 'Rp. ' + newTotal.toLocaleString('id-ID');
                                        } else {
                                            previewDiv.style.display = 'none';
                                        }
                                    }
                                    
                                    var btnLabel = document.getElementById('labelNominal_{{ $index }}');
                                    if (btnLabel && !qtyInput) {
                                        btnLabel.textContent = 'Rp. ' + newTotal.toLocaleString('id-ID');
                                    }
                                }
                                function hitungTotalFee_{{ $index }}() { hitungTagihanUnifikasi_{{ $index }}(); }
                                function hitungPotonganFee_{{ $index }}() { hitungTagihanUnifikasi_{{ $index }}(); }
                                
                                setTimeout(function() {
                                    hitungTagihanUnifikasi_{{ $index }}();
                                }, 500);
                            </script>
                        @endif

                    <div class="alert alert-info">
                        <strong>Perhatian!</strong><br>
                        Pastikan bukti transfer sudah benar. Ekstensi file yang diperbolehkan adalah jpg, jpeg, png,
                        atau pdf. Ukuran maksimal 2MB.<br>
                        @if($needQuantity ?? false)
                            Tarif per {{ strtolower($satuanLabel ?? 'hari') }}: <b>Rp. {{ number_format($tarifSatuan ?? $data->salary, 0, ',', '.') }}</b>. Total tagihan akan dihitung berdasarkan jumlah {{ strtolower($satuanLabel ?? 'hari') }} kerja.
                        @else
                            Nominal pembayaran: <b id="labelNominal_{{ $index }}">Rp. {{ number_format($totalTagihan ?? $data->salary, 0, ',', '.') }}</b>.
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="proof_majikan_fee_{{ $index }}" class="font-weight-bold">Upload Bukti <span
                                class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="proof_majikan" id="proof_majikan_fee_{{ $index }}"
                                required accept=".jpg,.jpeg,.png,.pdf">
                            <label class="custom-file-label" for="proof_majikan_fee_{{ $index }}">Pilih File</label>
                            <small class="form-text text-muted">Format file: jpg, jpeg, png, pdf (Maks 2MB)</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
