{{-- Modal Verifikasi Pembayaran Majikan --}}
<div class="modal fade" id="verifyMajikanPaymentModal-{{ $index }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title"><i class="fas fa-search-dollar mr-2"></i> Verifikasi Pembayaran Majikan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <h6 class="font-weight-bold text-dark">Bukti Pembayaran — {{ \Carbon\Carbon::parse($month)->format('F Y') }}</h6>
                    <p class="text-muted mb-0">Pekerja: <strong>{{ $data->servant ? $data->servant->name : '-' }}</strong></p>
                </div>

                {{-- Preview bukti --}}
                @if ($salaryRecord && $salaryRecord->payment_majikan_image)
                    @php
                        $prevPath = storage_path('app/public/payments/' . $salaryRecord->payment_majikan_image);
                    @endphp
                    <div class="text-center mb-3 p-3 bg-light rounded border">
                        @if (file_exists($prevPath))
                            @if (Str::endsWith($salaryRecord->payment_majikan_image, ['.jpg', '.jpeg', '.png', '.gif']))
                                <img src="{{ route('getFile', ['path' => 'payments/' . dirname($salaryRecord->payment_majikan_image), 'fileName' => basename($salaryRecord->payment_majikan_image)]) }}"
                                    class="img-fluid rounded shadow" style="max-height: 400px; cursor: zoom-in;"
                                    onclick="window.open(this.src, '_blank')" alt="Bukti Pembayaran">
                            @elseif (Str::endsWith($salaryRecord->payment_majikan_image, ['.pdf']))
                                <iframe src="{{ route('getFile', ['path' => 'payments/' . dirname($salaryRecord->payment_majikan_image), 'fileName' => basename($salaryRecord->payment_majikan_image)]) }}"
                                    width="100%" height="400px" class="rounded border"></iframe>
                            @endif
                        @else
                            <p class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> File tidak ditemukan.</p>
                        @endif
                    </div>
                @endif

                <div class="row text-center">
                    <div class="col-6">
                        <small class="text-muted d-block">Tagihan Majikan</small>
                        <span class="font-weight-bold text-dark">Rp. {{ number_format($salaryRecord ? $salaryRecord->total_salary_majikan : 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Status</small>
                        @if($salaryRecord && $salaryRecord->payment_majikan_status == 'waiting')
                            <span class="badge badge-warning px-3 py-2">Menunggu Verifikasi</span>
                        @elseif($salaryRecord && $salaryRecord->payment_majikan_status == 'verified')
                            <span class="badge badge-success px-3 py-2">Terverifikasi</span>
                        @elseif($salaryRecord && $salaryRecord->payment_majikan_status == 'rejected')
                            <span class="badge badge-danger px-3 py-2">Ditolak</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                @if ($salaryRecord && $salaryRecord->payment_majikan_status == 'waiting')
                    <form action="{{ route('worker.verify-majikan-payment', $data->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="month" value="{{ \Carbon\Carbon::parse($month)->format('Y-m') }}">
                        <input type="hidden" name="action" value="verified">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-check-circle mr-1"></i> Verifikasi & Setujui
                        </button>
                    </form>
                    <form action="{{ route('worker.verify-majikan-payment', $data->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="month" value="{{ \Carbon\Carbon::parse($month)->format('Y-m') }}">
                        <input type="hidden" name="action" value="rejected">
                        <button type="submit" class="btn btn-danger px-4" onclick="return confirm('Yakin ingin menolak pembayaran ini? Majikan harus mengupload ulang.')">
                            <i class="fas fa-times-circle mr-1"></i> Tolak
                        </button>
                    </form>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
