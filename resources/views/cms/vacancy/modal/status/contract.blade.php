<div class="modal fade" id="contractModal-{{ $d->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload Kontrak</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST"
                action="{{ route('applicant-indie.upload', ['vacancy' => $d->vacancy_id, 'user' => $d->servant_id]) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label for="work_start_date">Tanggal Mulai Bekerja <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="work_start_date" name="work_start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="file_contract_{{ $d->id }}">Berkas Kontrak <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"
                                    id="file_contract_label_{{ $d->id }}">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file_contract_{{ $d->id }}"
                                    name="file_contract" aria-describedby="file_contract_label_{{ $d->id }}"
                                    accept="image/*, application/pdf" required>
                                <label class="custom-file-label" for="file_contract_{{ $d->id }}">Choose
                                    file</label>
                            </div>
                        </div>
                        <div id="previewFile_{{ $d->id }}" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('custom-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const closingDateInput = document.getElementById('work_start_date');
            const today = new Date();

            // Konversi ke timezone Indonesia (UTC+7)
            const utcOffset = 7 * 60 * 60 * 1000;
            const indonesiaTime = new Date(today.getTime() + (today.getTimezoneOffset() * 60 * 1000) + utcOffset);

            const year = indonesiaTime.getFullYear();
            const month = String(indonesiaTime.getMonth() + 1).padStart(2, '0');
            const date = String(indonesiaTime.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${date}`;

            closingDateInput.setAttribute('min', formattedDate);
        });

        document.querySelectorAll('[id^="file_contract_"]').forEach(input => {
            input.addEventListener('change', function(event) {
                const modalId = this.id.split('_')[2];
                const preview = document.getElementById(`previewFile_${modalId}`);
                const label = document.querySelector(`label[for="file_contract_${modalId}"]`);
                const file = event.target.files[0];

                if (!file) {
                    preview.innerHTML = 'Tidak ada file yang dipilih.';
                    label.textContent = 'Choose file';
                    return;
                }

                label.textContent = file.name;

                const reader = new FileReader();

                reader.onload = function() {
                    try {
                        if (file.type.startsWith('image/')) {
                            preview.innerHTML =
                                `<img src="${this.result}" alt="Preview" class="img-fluid">`;
                        } else if (file.type === 'application/pdf') {
                            preview.innerHTML =
                                `<iframe src="${this.result}" width="100%" height="300px"></iframe>`;
                        } else {
                            preview.innerHTML = 'Format file tidak didukung untuk preview.';
                        }
                    } catch (error) {
                        console.error('Error displaying preview:', error);
                        preview.innerHTML = 'Terjadi kesalahan saat menampilkan preview.';
                    }
                };

                reader.onerror = function() {
                    console.error('Error reading file:', this.error);
                    preview.innerHTML = 'Terjadi kesalahan saat membaca file.';
                };

                reader.readAsDataURL(file);
            });
        });
    </script>
@endpush
