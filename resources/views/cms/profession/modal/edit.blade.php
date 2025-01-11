<div class="modal fade" id="editModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ubah Profesi</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('professions.update', $data->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group text-left">
                        <label for="name">Nama Profesi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ $data->name }}" required>
                    </div>

                    <div class="form-group text-left">
                        <label for="file_draft_{{ $data->id }}">File Draft <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file_draft_{{ $data->id }}"
                                    name="file_draft" accept="image/*, application/pdf">
                                <label class="custom-file-label" for="file_draft_{{ $data->id }}"
                                    id="label_file_draft_{{ $data->id }}">
                                    {{ $data->file_draft ? basename($data->file_draft) : 'Choose file' }}
                                </label>
                            </div>
                        </div>
                        <div id="previewFile-{{ $data->id }}" class="mt-2">
                            @if ($data->file_draft)
                                @php
                                    $filePath = storage_path('app/public/professions/' . $data->file_draft);
                                @endphp

                                @if (file_exists($filePath))
                                    @if (Str::endsWith($data->file_draft, ['.jpg', '.jpeg', '.png', '.gif']))
                                        <img src="{{ route('getFile', ['path' => 'professions', 'fileName' => $data->file_draft]) }}" alt="Preview"
                                            class="img-fluid">
                                    @elseif (Str::endsWith($data->file_draft, ['.pdf']))
                                        <iframe src="{{ route('getFile', ['path' => 'professions', 'fileName' => $data->file_draft]) }}" width="100%"
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
            document.querySelectorAll('[id^="file_draft_"]').forEach(input => {
                input.addEventListener('change', function(event) {
                    const modalId = this.id.split('_')[2];
                    const preview = document.getElementById(`previewFile-${modalId}`);
                    const label = document.getElementById(`label_file_draft_${modalId}`);
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
                                `<img src="${this.result}" alt="Preview" class="img-fluid">`;
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
