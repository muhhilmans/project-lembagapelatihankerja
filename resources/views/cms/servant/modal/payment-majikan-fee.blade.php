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

                        @php
                            // Persiapkan data scheme untuk JS
                            $jsClientData = [];
                            $jsMitraData = [];
                            if ($data->scheme) {
                                $jsClientData = is_array($data->scheme->client_data) ? $data->scheme->client_data : [];
                            }
                        @endphp
                        <script>
                            function hitungTotalFee_{{ $index }}() {
                                var qty = parseInt(document.getElementById('quantity_fee_{{ $index }}').value) || 0;
                                var tarif = {{ $tarifSatuan ?? $data->salary }};
                                var gajiPokok = tarif * qty;
                                var clientData = @json($jsClientData);
                                
                                var clientFees = 0;
                                var rincianHtml = '';
                                rincianHtml += 'Gaji Pokok: Rp. ' + tarif.toLocaleString('id-ID') + ' × ' + qty + ' {{ $satuanLabel ?? "Hari" }} = <strong>Rp. ' + gajiPokok.toLocaleString('id-ID') + '</strong><br>';
                                
                                for (var i = 0; i < clientData.length; i++) {
                                    var fee = clientData[i];
                                    var amount = 0;
                                    if (fee.unit === '%') {
                                        amount = gajiPokok * (fee.value / 100);
                                        rincianHtml += (fee.name || 'Fee') + ': ' + fee.value + '% = Rp. ' + Math.ceil(amount).toLocaleString('id-ID') + '<br>';
                                    } else {
                                        amount = fee.value;
                                        rincianHtml += (fee.name || 'Fee') + ': Rp. ' + Math.ceil(amount).toLocaleString('id-ID') + '<br>';
                                    }
                                    clientFees += amount;
                                }
                                
                                var totalTagihan = Math.ceil(gajiPokok + clientFees);
                                
                                var previewDiv = document.getElementById('previewTotalFee_{{ $index }}');
                                var rincianSpan = document.getElementById('rincianFee_{{ $index }}');
                                var totalSpan = document.getElementById('totalBayarFee_{{ $index }}');
                                
                                if (qty > 0) {
                                    previewDiv.style.display = 'block';
                                    rincianSpan.innerHTML = rincianHtml;
                                    totalSpan.textContent = 'Rp. ' + totalTagihan.toLocaleString('id-ID');
                                } else {
                                    previewDiv.style.display = 'none';
                                }
                            }
                            
                            // Eksekusi trigger perhitungan awal saat halaman diload (jika ada nilai default)
                            setTimeout(function() {
                                if(document.getElementById('quantity_fee_{{ $index }}').value) {
                                    hitungTotalFee_{{ $index }}();
                                }
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
                            Nominal pembayaran: <b>Rp. {{ number_format($totalTagihan ?? $data->salary, 0, ',', '.') }}</b>.
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
