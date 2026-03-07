{{-- Modal Perpanjang Garansi --}}
<div class="modal fade" id="extendWarrantyModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="extendWarrantyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="extendWarrantyModalLabel">Perpanjang Garansi</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('worker.extend-warranty', $data->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Pilih opsi garansi baru untuk <strong>{{ $data->servant ? $data->servant->name : '-' }}</strong>.</p>
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Opsi Garansi Baru <span class="text-danger">*</span></label>
                        <select name="garansi_id" class="form-control" id="extendGaransiSelect-{{ $data->id }}" required onchange="setExtendGaransiPrice({{ $data->id }})">
                            <option value="">-- Pilih Garansi --</option>
                            @foreach($garansiOptions ?? [] as $garansi)
                                <option value="{{ $garansi->id }}" data-price="{{ $garansi->price }}" {{ $data->garansi_id == $garansi->id ? 'selected' : '' }}>
                                    {{ $garansi->name }} (Maks. {{ $garansi->max_replacements }}x Tukar)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3" id="extendGaransiInfoContainer-{{ $data->id }}" style="display: {{ $data->garansi_id && $data->garansi ? 'block' : 'none' }};">
                        <div class="alert alert-info py-2 mb-0">
                            <i class="fas fa-info-circle mr-1"></i> 
                            <span id="extendGaransiInfoText-{{ $data->id }}">
                                @if($data->garansi)
                                    <strong>{{ $data->garansi->name }} (Maks. {{ $data->garansi->max_replacements }}x Tukar)</strong> - Harga: Rp {{ number_format($data->garansi->price, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perpanjangan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tukar Pembantu --}}
<div class="modal fade" id="swapModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="swapModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="swapModalLabel">Konfirmasi Tukar Pembantu</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('worker.swap-servant', $data->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian:</strong> Aksi ini akan mengatur akhir kontrak <strong>{{ $data->servant ? $data->servant->name : '-' }}</strong> terhitung 1 bulan sejak tanggal mulai bekerja.
                    </div>
                    <p>Apakah Anda yakin ingin melakukan proses tukar pembantu ini? Setelah ini, Anda dapat memproses pembantu pengganti melalui lamaran lain di lowongan yang sama.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Tukar Pembantu</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Akhiri Kontrak --}}
<div class="modal fade" id="endContractModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="endContractModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="endContractModalLabel">Akhiri Kontrak</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('worker.end-contract', $data->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <strong>Perhatian:</strong> Aksi ini akan mengakhiri kontrak <strong>{{ $data->servant ? $data->servant->name : '-' }}</strong> pada hari ini juga.
                    </div>
                    <p>Apakah Anda yakin akan mengakhiri masa kerja pembantu ini secara permanen?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Akhiri Kontrak</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Perpanjang Kontrak --}}
<div class="modal fade" id="extendContractModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="extendContractModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-info">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="extendContractModalLabel">Perpanjang Kontrak</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('worker.extend-contract', $data->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Masukkan jumlah bulan untuk memperpanjang kontrak/masa kerja <strong>{{ $data->servant ? $data->servant->name : '-' }}</strong>.</p>
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Jumlah Bulan Perpanjangan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="extend_months" class="form-control" placeholder="Contoh: 6" min="1" required>
                            <div class="input-group-append">
                                <span class="input-group-text">Bulan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Simpan Perpanjangan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function setExtendGaransiPrice(id) {
        var select = document.getElementById('extendGaransiSelect-' + id);
        var infoContainer = document.getElementById('extendGaransiInfoContainer-' + id);
        var infoText = document.getElementById('extendGaransiInfoText-' + id);

        if (select && select.value) {
            var selectedOption = select.options[select.selectedIndex];
            var price = selectedOption.getAttribute('data-price');
            var nameText = selectedOption.text;
            
            infoContainer.style.display = 'block';
            if (price) {
                var formattedPrice = parseFloat(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                infoText.innerHTML = "<strong>" + nameText.trim() + "</strong> - Harga: Rp " + formattedPrice;
            }
        } else if (infoContainer) {
            infoContainer.style.display = 'none';
            if(infoText) infoText.innerHTML = '';
        }
    }
</script>
