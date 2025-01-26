<div class="modal fade" id="paymentAdminModal-{{ $salary->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload Bukti Pembayaran</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('payment-admin.upload', ['app' => $salary->application_id, 'salary' => $salary->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group text-left">
                        <label for="proof_admin_{{ $salary->id }}">Bukti Pembayaran <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="proof_admin_{{ $salary->id }}"
                                    name="proof_admin" accept="image/*, application/pdf">
                                <label class="custom-file-label" for="proof_admin_{{ $salary->id }}"
                                    id="label_proof_admin_{{ $salary->id }}">
                                    {{ $salary->payment_pembantu_image ? basename($salary->payment_pembantu_image) : 'Choose file' }}
                                </label>
                            </div>
                        </div>
                        <div id="previewFileAdmin-{{ $salary->id }}" class="mt-2">
                            @if ($salary->payment_pembantu_image)
                                @php
                                    $filePath = storage_path('app/public/payments/' . $salary->payment_pembantu_image);
                                @endphp

                                @if (file_exists($filePath))
                                    @if (Str::endsWith($salary->payment_pembantu_image, ['.jpg', '.jpeg', '.png', '.gif']))
                                        <img src="{{ route('getFile', ['path' => 'payments', 'fileName' => $salary->payment_pembantu_image]) }}" alt="Preview"
                                            class="img-fluid zoomable-image" style="max-height: 300px;">
                                    @elseif (Str::endsWith($salary->payment_pembantu_image, ['.pdf']))
                                        <iframe src="{{ route('getFile', ['path' => 'payments', 'fileName' => $salary->payment_pembantu_image]) }}" width="100%"
                                            height="300px"></iframe>
                                    @else
                                        <p>Format file tidak didukung untuk preview.</p>
                                    @endif
                                @else
                                    <p>File tidak ditemukan di server.</p>
                                @endif
                            @else
                                <p>Belum ada file yang diunggah.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-warning" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('custom-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[id^="proof_admin_"]').forEach(input => {
                input.addEventListener('change', function(event) {
                    const modalId = this.id.split('_')[2];
                    const preview = document.getElementById(`previewFileAdmin-${modalId}`);
                    const label = document.getElementById(`label_proof_admin_${modalId}`);
                    const file = event.target.files[0];

                    if (!file) {
                        preview.innerHTML = 'Tidak ada file yang dipilih.';
                        label.textContent = 'Choose file';
                        return;
                    }

                    label.textContent = file.name;

                    const reader = new FileReader();

                    reader.onload = function() {
                        if (file.type.startsWith('image/')) {
                            preview.innerHTML =
                                `<img src="${this.result}" alt="Preview" class="img-fluid zoomable-image" style="max-width: 100%; max-height: 300px;">`;
                        } else if (file.type === 'application/pdf') {
                            preview.innerHTML =
                                `<iframe src="${this.result}" width="100%" height="300px"></iframe>`;
                        } else {
                            preview.innerHTML = 'Format file tidak didukung untuk preview.';
                        }
                    };

                    reader.onerror = function() {
                        console.error('Error reading file:', this.error);
                        preview.innerHTML = 'Terjadi kesalahan saat membaca file.';
                    };

                    reader.readAsDataURL(file);
                });
            });
        });
    </script>
@endpush
