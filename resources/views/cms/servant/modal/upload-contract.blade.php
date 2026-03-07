{{-- Modal Upload Kontrak --}}
<div class="modal fade" id="uploadContractModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="uploadContractModalLabel-{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadContractModalLabel-{{ $data->id }}">
                    <i class="fas fa-upload mr-1"></i> Upload Kontrak
                </h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST"
                action="{{ auth()->user()->hasRole('admin|superadmin') ? route('worker.upload-contract-admin', $data->id) : route('worker.upload-contract', $data->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label for="file_contract_upload_{{ $data->id }}">Berkas Kontrak <span class="text-danger">*</span></label>
                        <small class="text-muted d-block mb-2">Format: JPG, JPEG, PNG, PDF (Maks. 5MB)</small>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="file_contract_upload_label_{{ $data->id }}">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file_contract_upload_{{ $data->id }}"
                                    name="file_contract" aria-describedby="file_contract_upload_label_{{ $data->id }}"
                                    accept="image/*, application/pdf" required>
                                <label class="custom-file-label" for="file_contract_upload_{{ $data->id }}">Pilih file</label>
                            </div>
                        </div>
                        <div id="previewContractFile_{{ $data->id }}" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-upload mr-1"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('custom-script')
    <script>
        (function() {
            const inputContract = document.getElementById('file_contract_upload_{{ $data->id }}');
            if (inputContract) {
                inputContract.addEventListener('change', function(event) {
                    const preview = document.getElementById('previewContractFile_{{ $data->id }}');
                    const label = document.querySelector('label[for="file_contract_upload_{{ $data->id }}"]');
                    const file = event.target.files[0];

                    if (!file) {
                        preview.innerHTML = '';
                        label.textContent = 'Pilih file';
                        return;
                    }

                    label.textContent = file.name;

                    const reader = new FileReader();
                    reader.onload = function() {
                        try {
                            if (file.type.startsWith('image/')) {
                                preview.innerHTML = `<img src="${this.result}" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">`;
                            } else if (file.type === 'application/pdf') {
                                preview.innerHTML = `<iframe src="${this.result}" width="100%" height="250px" class="rounded"></iframe>`;
                            } else {
                                preview.innerHTML = '<small class="text-warning">Format file tidak didukung untuk preview.</small>';
                            }
                        } catch (error) {
                            preview.innerHTML = '<small class="text-danger">Gagal menampilkan preview.</small>';
                        }
                    };
                    reader.onerror = function() {
                        preview.innerHTML = '<small class="text-danger">Gagal membaca file.</small>';
                    };
                    reader.readAsDataURL(file);
                });
            }
        })();
    </script>
@endpush
