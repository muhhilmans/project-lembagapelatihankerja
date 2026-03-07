<div class="modal fade" id="paymentMajikanContractModal-{{ $index }}" tabindex="-1" role="dialog"
    aria-labelledby="paymentMajikanContractLabel-{{ $index }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="paymentMajikanContractLabel-{{ $index }}">Upload Bukti
                    Pembayaran (Kontrak - Bulan {{ \Carbon\Carbon::parse($month)->format('F Y') }})</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('payment-majikan-contract.upload', $data->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <div class="alert alert-info">
                        <strong>Perhatian!</strong><br>
                        Pastikan bukti transfer sudah benar. Ekstensi file yang diperbolehkan adalah jpg, jpeg, png,
                        atau pdf. Ukuran maksimal 2MB. Nominal pembayaran: <b>Rp. {{ number_format($data->salary, 0, ',', '.') }}</b>.
                    </div>
                    <div class="form-group">
                        <label for="proof_majikan" class="font-weight-bold">Upload Bukti <span
                                class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="proof_majikan" id="proof_majikan"
                                required accept=".jpg,.jpeg,.png,.pdf">
                            <label class="custom-file-label" for="proof_majikan">Pilih File</label>
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
