<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Profesi</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('professions.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama Profesi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="file_draft">File Draft <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"
                                    id="file_draft">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file_draft"
                                    name="file_draft" aria-describedby="file_draft"
                                    accept="image/*, application/pdf" required>
                                <label class="custom-file-label" for="file_draft" id="label_file_draft">Choose
                                    file</label>
                            </div>
                        </div>
                        <div id="previewFile" class="mt-2"></div>
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
        document.querySelectorAll('#file_draft').forEach(input => {
            input.addEventListener('change', function(event) {
                const preview = document.getElementById(`previewFile`);
                const label = document.querySelectorAll('#label_file_draft')[0];
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