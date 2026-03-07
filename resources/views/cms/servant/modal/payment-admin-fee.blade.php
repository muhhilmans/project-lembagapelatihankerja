<div class="modal fade" id="paymentAdminFeeModal-{{ $index }}" tabindex="-1" role="dialog"
    aria-labelledby="paymentAdminFeeLabel-{{ $index }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="paymentAdminFeeLabel-{{ $index }}">Pembayaran Ke Mitra (Fee - Bulan {{ \Carbon\Carbon::parse($month)->format('F Y') }})</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('payment-admin-contract.upload', $data->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="month" value="{{ $month }}">
                    
                    @if ($salaryRecord && $salaryRecord->payment_majikan_image)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle mr-1"></i> Majikan sudah mengupload bukti pembayaran.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Majikan belum mengupload bukti pembayaran. Anda tetap dapat melakukan upload.
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <strong>Perhatian!</strong><br>
                        Pastikan bukti transfer Anda kepada mitra sudah benar.
                        Nominal Gaji Pekerja: <b>Rp. {{ number_format($data->salary, 0, ',', '.') }}</b>.
                    </div>
                    
                    <div class="form-group">
                        <label for="proof_admin_fee_{{ $index }}" class="font-weight-bold">Upload Bukti Transfer Ke Pembantu <span
                                class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="proof_admin" id="proof_admin_fee_{{ $index }}"
                                required accept=".jpg,.jpeg,.png,.pdf">
                            <label class="custom-file-label" for="proof_admin_fee_{{ $index }}">Pilih File</label>
                            <small class="form-text text-muted">Format file: jpg, jpeg, png, pdf (Maks 2MB)</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
