<div class="modal fade" id="verifyWarrantyPaymentModal-{{ $wp->id }}" tabindex="-1" role="dialog" aria-labelledby="verifyWarrantyPaymentLabel-{{ $wp->id }}" aria-hidden="true">
    <div class="modal-dialog text-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="verifyWarrantyPaymentLabel-{{ $wp->id }}">Verifikasi Pembayaran Garansi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('worker.warranty.verify', $wp->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Verifikasi bukti transfer pembayaran tagihan garansi ini (Periode: <strong>{{ \Carbon\Carbon::parse($wp->month_date)->format('F Y') }}</strong>).</p>
                    
                    @if ($wp->payment_image)
                        <div class="mb-3 text-center">
                            @if (Str::endsWith($wp->payment_image, ['.jpg', '.jpeg', '.png', '.gif']))
                                <img src="{{ route('getFile', ['path' => 'warranty_payments', 'fileName' => basename($wp->payment_image)]) }}" class="img-fluid zoomable-image rounded shadow-sm" style="max-height: 250px;">
                            @else
                                <a href="{{ route('getFile', ['path' => 'warranty_payments', 'fileName' => basename($wp->payment_image)]) }}" target="_blank" class="btn btn-info"><i class="fas fa-file-pdf mr-1"></i> Buka File PDF</a>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">Majikan belum mengupload bukti bayar.</div>
                    @endif

                    <div class="form-group mt-4">
                        <label for="status_{{ $wp->id }}">Status Verifikasi <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" id="status_{{ $wp->id }}" required>
                            <option value="">Pilih Status...</option>
                            <option value="paid" {{ $wp->status == 'paid' ? 'selected' : '' }}>Setujui / Lunas</option>
                            <option value="pending" {{ $wp->status == 'pending' ? 'selected' : '' }}>Tolak / Belum Sesuai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
