<div class="modal fade" id="acceptModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Verifikasi Pengaduan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('complaints.change', $data->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <input type="text" name="status" value="accepted" hidden>

                    <div class="form-group">
                        <label for="file_{{ $data->id }}">Surat Peringatan</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"
                                    id="file_label_{{ $data->id }}">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file_{{ $data->id }}"
                                    name="file" aria-describedby="file_label_{{ $data->id }}"
                                    accept="image/*, application/pdf">
                                <label class="custom-file-label" for="file_{{ $data->id }}">Choose
                                    file</label>
                            </div>
                        </div>
                        <div id="previewFile_{{ $data->id }}" class="mt-2"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Yakin</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('custom-script')
    <script>
        document.querySelectorAll('[id^="file_"]').forEach(input => {
            input.addEventListener('change', function(event) {
                const modalId = this.id.split('_')[1];
                const preview = document.getElementById(`previewFile_${modalId}`);
                const label = document.querySelector(`label[for="file_${modalId}"]`);
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