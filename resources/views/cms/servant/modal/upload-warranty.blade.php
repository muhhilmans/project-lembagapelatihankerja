<div class="modal fade" id="uploadWarrantyPaymentModal-{{ $wp->id }}" tabindex="-1" role="dialog" aria-labelledby="uploadWarrantyPaymentLabel-{{ $wp->id }}" aria-hidden="true">
    <div class="modal-dialog text-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="uploadWarrantyPaymentLabel-{{ $wp->id }}">Upload Pembayaran Garansi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('worker.warranty.upload', $wp->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p>Silakan upload bukti transfer pembayaran tagihan garansi untuk periode bulan <strong>{{ \Carbon\Carbon::parse($wp->month_date)->format('F Y') }}</strong> sebesar <strong>Rp. {{ number_format($wp->amount, 0, ',', '.') }}</strong>.</p>
                    
                    <div class="form-group mb-3">
                        <label for="proof_file_{{ $wp->id }}">Bukti Transfer (Image/PDF) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="proof_file_{{ $wp->id }}" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="text-muted">Maksimal ukuran file 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Upload Bukti</button>
                </div>
            </form>
        </div>
    </div>
</div>
